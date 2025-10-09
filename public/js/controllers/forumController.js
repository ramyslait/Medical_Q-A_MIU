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
    this.discussions = this.generateDummyDiscussions();
  }

  generateDummyDiscussions() {
    return [
      {
        id: "disc-001",
        title: "Managing chronic pain - what works for you?",
        category: "support",
        preview:
          "I've been dealing with chronic back pain for over a year now. I've tried various treatments but nothing seems to provide long-term relief. What strategies have worked for others in similar situations?",
        author: "John Smith",
        authorAvatar: "https://via.placeholder.com/50/2563eb/ffffff?text=JS",
        replies: 12,
        views: 45,
        lastActivity: "2 hours ago",
        createdAt: new Date(Date.now() - 2 * 60 * 60 * 1000).toISOString(),
      },
      {
        id: "disc-002",
        title: "New medication side effects - when to worry?",
        category: "treatments",
        preview:
          "Started a new prescription last week and I'm experiencing some side effects. How do you know when side effects are normal vs. when you should contact your doctor?",
        author: "Maria Chen",
        authorAvatar: "https://via.placeholder.com/50/059669/ffffff?text=MC",
        replies: 8,
        views: 32,
        lastActivity: "4 hours ago",
        createdAt: new Date(Date.now() - 4 * 60 * 60 * 1000).toISOString(),
      },
      {
        id: "disc-003",
        title: "Tips for maintaining a healthy lifestyle with diabetes",
        category: "lifestyle",
        preview:
          "Recently diagnosed with Type 2 diabetes and looking for practical tips on managing diet, exercise, and lifestyle changes. What has helped you the most?",
        author: "Emily Rodriguez",
        authorAvatar: "https://via.placeholder.com/50/06b6d4/ffffff?text=ER",
        replies: 15,
        views: 67,
        lastActivity: "6 hours ago",
        createdAt: new Date(Date.now() - 6 * 60 * 60 * 1000).toISOString(),
      },
      {
        id: "disc-004",
        title: "Understanding anxiety symptoms - seeking support",
        category: "symptoms",
        preview:
          "I've been experiencing anxiety symptoms for the past few months. Feeling overwhelmed and not sure if what I'm experiencing is normal anxiety or something more serious.",
        author: "Alex Lee",
        authorAvatar: "https://via.placeholder.com/50/dc2626/ffffff?text=AL",
        replies: 23,
        views: 89,
        lastActivity: "1 day ago",
        createdAt: new Date(Date.now() - 24 * 60 * 60 * 1000).toISOString(),
      },
      {
        id: "disc-005",
        title: "Sleep hygiene tips for better rest",
        category: "lifestyle",
        preview:
          "Struggling with insomnia lately and looking for evidence-based tips to improve sleep quality. What routines or changes have made the biggest difference for you?",
        author: "Sarah Wilson",
        authorAvatar: "https://via.placeholder.com/50/7c3aed/ffffff?text=SW",
        replies: 18,
        views: 124,
        lastActivity: "2 days ago",
        createdAt: new Date(Date.now() - 2 * 24 * 60 * 60 * 1000).toISOString(),
      },
    ];
  }

  async getDiscussions(category = null, sortBy = "recent") {
    await new Promise((resolve) => setTimeout(resolve, 500));

    let discussions = [...this.discussions];

    // Filter by category
    if (category) {
      discussions = discussions.filter((d) => d.category === category);
    }

    // Sort discussions
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
    await new Promise((resolve) => setTimeout(resolve, 300));
    return this.discussions.find((d) => d.id === id);
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
                        <span class="discussion-author">by ${
                          discussion.author
                        }</span>
                        <span class="discussion-time">${
                          discussion.lastActivity
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
  if (window.location.pathname.includes("forum.html")) {
    new ForumController();
  }
});
