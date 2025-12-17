/**
 * Forum Controller
 * Handles forum functionality and discussion management
 */

class ForumController {
  constructor() {
    this.forumView = new ForumView();
    this.forumModel = new ForumModel();
    this.init();
  }

  init() {
    this.loadDiscussions();
    this.bindEvents();
  }

  bindEvents() {
    // Filter functionality
    const categoryFilter = document.getElementById("categoryFilter");
    const sortFilter = document.getElementById("sortFilter");

    if (categoryFilter) {
      categoryFilter.addEventListener("change", () => this.filterDiscussions());
    }

    if (sortFilter) {
      sortFilter.addEventListener("change", () => this.sortDiscussions());
    }
  }

  async loadDiscussions() {
    try {
      const discussions = await this.forumModel.getDiscussions();
      this.forumView.renderDiscussions(discussions);
    } catch (error) {
      MediQA.showNotification("Failed to load discussions", "error");
    }
  }

  async filterDiscussions() {
    const category = document.getElementById("categoryFilter").value;
    const discussions = await this.forumModel.getDiscussions(category);
    this.forumView.renderDiscussions(discussions);
  }

  async sortDiscussions() {
    const sortBy = document.getElementById("sortFilter").value;
    const discussions = await this.forumModel.getDiscussions(null, sortBy);
    this.forumView.renderDiscussions(discussions);
  }

  showApproveModal(questionId) {
    this.forumView.showApproveModal(questionId);
  }

  showDisapproveModal(questionId) {
    this.forumView.showDisapproveModal(questionId);
  }

  async submitReview(formData) {
    await this.forumView.submitReview(formData);
    // Reload discussions after review
    await this.loadDiscussions();
  }
}

/**
 * Forum Model
 * Handles forum data operations
 */
class ForumModel {
  constructor() {
  }

  async getDiscussions(category = null, sortBy = "recent") {
    // Fetch real questions (with AI answers) from backend API
    const params = new URLSearchParams();
    if (category) params.append("category", category);
    if (sortBy) params.append("sort", sortBy);

    const response = await fetch(
      `api/getQuestions.php${params.toString() ? `?${params.toString()}` : ""}`
    );

    if (!response.ok) {
      throw new Error("Failed to fetch questions");
    }

    const data = await response.json();
    if (!data.success) {
      throw new Error(data.error || "Unknown error while loading questions");
    }

    // Map API questions into discussion structure used by the view
    const discussions = data.questions.map((q) => {
      const authorName = q.user_name || "Anonymous";
      const initials = authorName
        .split(" ")
        .map((p) => p[0])
        .join("")
        .substring(0, 2)
        .toUpperCase();

      return {
        id: q.id,
        title: q.title,
        category: q.category || "general",
        preview: q.preview || q.body,
        author: authorName,
        authorAvatar: `https://via.placeholder.com/50/2563eb/ffffff?text=${encodeURIComponent(
          initials || "U"
        )}`,
        replies: q.replies || 0,
        views: q.views || 0,
        lastActivity: q.time_ago || "",
        createdAt: q.created_at,
        aiAnswer: q.ai_answer || "",
        doctorApprovalStatus: q.doctor_approval_status || "pending",
        doctorAnswer: q.doctor_answer || null,
        doctorComment: q.doctor_comment || null,
        doctorName: q.doctor_name || null,
        doctorReviewedAt: q.doctor_reviewed_at || null,
      };
    });

    // Client-side sorting fallback if backend doesn't handle it
    switch (sortBy) {
      case "popular":
        discussions.sort((a, b) => b.views - a.views);
        break;
      case "replies":
        discussions.sort((a, b) => b.replies - a.replies);
        break;
      case "recent":
      default:
        discussions.sort(
          (a, b) => new Date(b.createdAt) - new Date(a.createdAt)
        );
        break;
    }

    return discussions;
  }

  async getDiscussionById(id) {
    // For now, re-use getDiscussions and find by id.
    const discussions = await this.getDiscussions();
    return discussions.find((d) => d.id === id);
  }
}

/**
 * Forum View
 * Handles forum UI updates
 */
class ForumView {
  constructor() {
    this.init();
  }

  init() {
    this.initializeFAQAccordions();
  }

  renderDiscussions(discussions) {
    const discussionsList = document.querySelector(".discussions-list");
    if (discussionsList) {
      discussionsList.innerHTML = discussions
        .map((discussion) => this.renderDiscussionItem(discussion))
        .join("");
    }
  }

  renderDiscussionItem(discussion) {
    const categoryClasses = {
      support: "badge-info",
      treatments: "badge-warning",
      lifestyle: "badge-success",
      symptoms: "badge-danger",
      general: "badge-info",
    };

    const approvalStatusClasses = {
      approved: "badge-success",
      pending: "badge-warning",
      not_approved: "badge-danger",
    };

    const approvalStatusLabels = {
      approved: "Approved",
      pending: "Pending Review",
      not_approved: "Not Approved",
    };

    const isDoctor = window.currentUserRole === "doctor";
    const canReview = isDoctor && discussion.doctorApprovalStatus === "pending";

    return `
            <div class="discussion-item">
                <div class="discussion-avatar">
                    <img src="${discussion.authorAvatar}" alt="User Avatar">
                </div>
                <div class="discussion-content">
                    <div class="discussion-header">
                        <h3 class="discussion-title">
                            ${discussion.title}
                        </h3>
                        <div class="discussion-badges">
                            <span class="discussion-category badge ${
                              categoryClasses[discussion.category] || "badge-info"
                            }">${discussion.category}</span>
                            <span class="approval-status badge ${
                              approvalStatusClasses[discussion.doctorApprovalStatus] || "badge-warning"
                            }">
                                <i class="fas ${
                                  discussion.doctorApprovalStatus === "approved" ? "fa-check-circle" :
                                  discussion.doctorApprovalStatus === "not_approved" ? "fa-times-circle" :
                                  "fa-clock"
                                }"></i>
                                ${approvalStatusLabels[discussion.doctorApprovalStatus] || "Pending Review"}
                            </span>
                        </div>
                    </div>
                    <p class="discussion-preview">${this.escapeHtml(discussion.preview)}</p>
                    <div class="discussion-meta">
                        <span class="discussion-author">by ${this.escapeHtml(discussion.author)}</span>
                        <span class="discussion-time">${
                          discussion.lastActivity || ""
                        }</span>
                        <span class="discussion-replies">
                            <i class="fas fa-comments"></i>
                            ${discussion.replies} replies
                        </span>
                        <span class="discussion-views">
                            <i class="fas fa-eye"></i>
                            ${discussion.views} views
                        </span>
                    </div>
                    
                    ${discussion.aiAnswer ? `
                    <div class="discussion-ai-answer">
                        <div class="ai-answer-header">
                            <h4 class="ai-answer-title">
                                <i class="fas fa-robot"></i>
                                AI Generated Answer
                            </h4>
                        </div>
                        <p class="ai-answer-body">${this.escapeHtml(discussion.aiAnswer)}</p>
                    </div>
                    ` : ""}
                    
                    ${discussion.doctorApprovalStatus === "approved" && discussion.doctorComment ? `
                    <div class="discussion-doctor-answer">
                        <div class="doctor-answer-header">
                            <h4 class="doctor-answer-title">
                                <i class="fas fa-user-md"></i>
                                Doctor's Review
                                ${discussion.doctorName ? `<span class="doctor-name">by ${this.escapeHtml(discussion.doctorName)}</span>` : ""}
                            </h4>
                        </div>
                        <p class="doctor-comment">${this.escapeHtml(discussion.doctorComment)}</p>
                    </div>
                    ` : ""}
                    
                    ${discussion.doctorApprovalStatus === "not_approved" && discussion.doctorAnswer ? `
                    <div class="discussion-doctor-answer">
                        <div class="doctor-answer-header">
                            <h4 class="doctor-answer-title">
                                <i class="fas fa-user-md"></i>
                                Doctor's Answer
                                ${discussion.doctorName ? `<span class="doctor-name">by ${this.escapeHtml(discussion.doctorName)}</span>` : ""}
                            </h4>
                        </div>
                        <p class="doctor-answer-body">${this.escapeHtml(discussion.doctorAnswer)}</p>
                    </div>
                    ` : ""}
                    
                    ${canReview ? `
                    <div class="doctor-review-panel">
                        <p class="review-panel-title">Review AI Answer</p>
                        <div class="review-actions">
                            <button class="btn btn-small btn-success" onclick="if (window.forumController) window.forumController.showApproveModal(${discussion.id})">
                                <i class="fas fa-check"></i> Approve
                            </button>
                            <button class="btn btn-small btn-danger" onclick="if (window.forumController) window.forumController.showDisapproveModal(${discussion.id})">
                                <i class="fas fa-times"></i> Disapprove
                            </button>
                        </div>
                    </div>
                    ` : ""}
                </div>
            </div>
        `;
  }

  escapeHtml(text) {
    if (!text) return "";
    const div = document.createElement("div");
    div.textContent = text;
    return div.innerHTML;
  }

  showApproveModal(questionId) {
    const modal = document.createElement("div");
    modal.className = "review-modal-overlay active";
    modal.innerHTML = `
      <div class="review-modal">
        <div class="review-modal-header">
          <h3>Approve AI Answer</h3>
          <button class="review-modal-close" onclick="this.closest('.review-modal-overlay').remove()">&times;</button>
        </div>
        <div class="review-modal-body">
          <form id="approveForm">
            <input type="hidden" name="question_id" value="${questionId}">
            <input type="hidden" name="action" value="approve">
            <div class="form-group">
              <label class="form-label">Add your comment (optional)</label>
              <textarea class="form-textarea" name="doctor_comment" rows="4" placeholder="Add any additional notes or clarifications..."></textarea>
            </div>
            <div class="review-modal-footer">
              <button type="button" class="btn btn-outline" onclick="this.closest('.review-modal-overlay').remove()">Cancel</button>
              <button type="submit" class="btn btn-success">Approve Answer</button>
            </div>
          </form>
        </div>
      </div>
    `;
    document.body.appendChild(modal);

      modal.querySelector("#approveForm").addEventListener("submit", async (e) => {
      e.preventDefault();
      const formData = new FormData(e.target);
      if (window.forumController) {
        await window.forumController.submitReview(formData);
      }
      modal.remove();
    });

    modal.addEventListener("click", (e) => {
      if (e.target === modal) {
        modal.remove();
      }
    });
  }

  showDisapproveModal(questionId) {
    const modal = document.createElement("div");
    modal.className = "review-modal-overlay active";
    modal.innerHTML = `
      <div class="review-modal">
        <div class="review-modal-header">
          <h3>Disapprove AI Answer</h3>
          <button class="review-modal-close" onclick="this.closest('.review-modal-overlay').remove()">&times;</button>
        </div>
        <div class="review-modal-body">
          <form id="disapproveForm">
            <input type="hidden" name="question_id" value="${questionId}">
            <input type="hidden" name="action" value="disapprove">
            <div class="form-group">
              <label class="form-label">Provide your answer <span style="color: #dc2626;">*</span></label>
              <textarea class="form-textarea" name="doctor_answer" rows="6" placeholder="Please provide your professional answer to replace the AI answer..." required></textarea>
            </div>
            <div class="review-modal-footer">
              <button type="button" class="btn btn-outline" onclick="this.closest('.review-modal-overlay').remove()">Cancel</button>
              <button type="submit" class="btn btn-danger">Submit Your Answer</button>
            </div>
          </form>
        </div>
      </div>
    `;
    document.body.appendChild(modal);

    modal.querySelector("#disapproveForm").addEventListener("submit", async (e) => {
      e.preventDefault();
      const formData = new FormData(e.target);
      if (window.forumController) {
        await window.forumController.submitReview(formData);
      }
      modal.remove();
    });

    modal.addEventListener("click", (e) => {
      if (e.target === modal) {
        modal.remove();
      }
    });
  }

  async submitReview(formData) {
    try {
      const response = await fetch("api/reviewQuestion.php", {
        method: "POST",
        body: formData,
        credentials: "same-origin",
      });

      const data = await response.json();

      if (data.success) {
        MediQA.showNotification("Review submitted successfully", "success");
      } else {
        MediQA.showNotification(data.error || "Failed to submit review", "error");
      }
    } catch (error) {
      console.error("Error submitting review:", error);
      MediQA.showNotification("An error occurred while submitting review", "error");
    }
  }

  initializeFAQAccordions() {
    // FAQ accordion functionality
    document.querySelectorAll(".faq-question").forEach((question) => {
      question.addEventListener("click", () => {
        const faqItem = question.parentElement;
        const answer = faqItem.querySelector(".faq-answer");
        const icon = question.querySelector("i");

        // Close other open items
        document.querySelectorAll(".faq-item").forEach((item) => {
          if (item !== faqItem) {
            item.classList.remove("active");
            item.querySelector(".faq-answer").style.maxHeight = "0";
            item.querySelector(".faq-question i").style.transform =
              "rotate(0deg)";
          }
        });

        // Toggle current item
        faqItem.classList.toggle("active");
        if (faqItem.classList.contains("active")) {
          answer.style.maxHeight = answer.scrollHeight + "px";
          icon.style.transform = "rotate(180deg)";
        } else {
          answer.style.maxHeight = "0";
          icon.style.transform = "rotate(0deg)";
        }
      });
    });
  }
}

// Initialize forum controller when DOM is loaded
let forumController = null;
document.addEventListener("DOMContentLoaded", function () {
  // Initialize on the forum page (PHP route)
  if (document.querySelector(".discussions-list")) {
    forumController = new ForumController();
    window.forumController = forumController; // Make it globally accessible
  }
});
