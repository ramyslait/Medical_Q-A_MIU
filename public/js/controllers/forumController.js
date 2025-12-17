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

    return `
            <div class="discussion-item">
                <div class="discussion-avatar">
                    <img src="${discussion.authorAvatar}" alt="User Avatar">
                </div>
                <div class="discussion-content">
                    <div class="discussion-header">
                        <h3 class="discussion-title">
                            <a href="discussion-detail.html?id=${
                              discussion.id
                            }">${discussion.title}</a>
                        </h3>
                        <span class="discussion-category badge ${
                          categoryClasses[discussion.category] || "badge-info"
                        }">${discussion.category}</span>
                    </div>
                    <p class="discussion-preview">${discussion.preview}</p>
                    <div class="discussion-meta">
                        <span class="discussion-author">by ${discussion.author}</span>
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
                    ${
                      discussion.aiAnswer
                        ? `<div class="discussion-ai-answer">
                              <h4 class="ai-answer-title">AI Answer</h4>
                              <p class="ai-answer-body">${discussion.aiAnswer}</p>
                           </div>`
                        : ""
                    }
                </div>
            </div>
        `;
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
document.addEventListener("DOMContentLoaded", function () {
  // Initialize on the forum page (PHP route)
  if (document.querySelector(".discussions-list")) {
    new ForumController();
  }
});
