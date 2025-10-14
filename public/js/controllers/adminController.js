/**
 * Admin Dashboard Controller
 * Handles admin dashboard functionality and data management
 */

class AdminController {
  constructor() {
    this.dashboardView = new DashboardView();
    this.adminModel = new AdminModel();
    this.currentSection = "dashboard";
    this.init();
  }

  init() {
    this.checkAdminAccess();
    this.initializeNavigation();
    this.loadDashboardData();
    this.bindEvents();
  }

  checkAdminAccess() {
    if (!MediQA.requireAdmin()) {
      return;
    }
  }

  initializeNavigation() {
    // Handle sidebar navigation
    document.querySelectorAll(".sidebar-link").forEach((link) => {
      link.addEventListener("click", (e) => {
        e.preventDefault();
        const section = e.currentTarget.dataset.section;
        this.showSection(section);
        this.updateActiveNavLink(e.currentTarget);
      });
    });

    // Handle sidebar toggle
    window.toggleSidebar = () => {
      const sidebar = document.getElementById("sidebar");
      sidebar.classList.toggle("active");
    };
  }

  bindEvents() {
    // Search functionality
    const searchInput = document.querySelector(".search-input");
    if (searchInput) {
      searchInput.addEventListener("input", (e) => {
        this.handleSearch(e.target.value);
      });
    }

    // Notification button
    const notificationBtn = document.querySelector(
      '.action-btn[title="Notifications"]'
    );
    if (notificationBtn) {
      notificationBtn.addEventListener("click", () => {
        this.showNotifications();
      });
    }
  }

  async loadDashboardData() {
  fetch('api/getUsersCount.php', { credentials: 'same-origin' })
    .then(response => {
      if (!response.ok) throw new Error(`HTTP ${response.status}`);
      return response.json();
    })
    .then(data => {
      const el = document.getElementById('userCount');
      const el1 = document.getElementById('userCount1');
      if (!el || !el1) return;
      if (data && data.success) {
        el.textContent = Number(data.count).toLocaleString();
        el1.textContent = Number(data.count).toLocaleString();
      } else {
        console.error('Error:', data && data.error);
        el.textContent = '—';
        el1.textContent = '—';
      }
    })
    .catch(err => {
      console.error('Fetch error:', err);
      const el = document.getElementById('userCount');
      if (el) el.textContent = '—';
    });

}


  showSection(sectionName) {
    // Hide all sections
    document.querySelectorAll(".dashboard-section").forEach((section) => {
      section.classList.remove("active");
    });

    // Show selected section
    const targetSection = document.getElementById(`${sectionName}-section`);
    if (targetSection) {
      targetSection.classList.add("active");
    }

    // Update page title
    const pageTitle = document.getElementById("pageTitle");
    if (pageTitle) {
      pageTitle.textContent = this.getSectionTitle(sectionName);
    }

    // Load section-specific data
    this.loadSectionData(sectionName);

    this.currentSection = sectionName;
  }

  updateActiveNavLink(activeLink) {
    // Remove active class from all links
    document.querySelectorAll(".sidebar-link").forEach((link) => {
      link.classList.remove("active");
    });

    // Add active class to clicked link
    activeLink.classList.add("active");
  }

  getSectionTitle(sectionName) {
    const titles = {
      dashboard: "Dashboard",
      users: "User Management",
      questions: "Question Management",
      answers: "Answer Management",
      forum: "Forum Management",
      analytics: "Analytics",
      settings: "Settings",
    };
    return titles[sectionName] || "Dashboard";
  }

  async loadSectionData(sectionName) {
    switch (sectionName) {
      case "dashboard":
        await this.loadDashboardData();
        break;
      case "users":
        await this.loadUsersData();
        break;
      case "questions":
        await this.loadQuestionsData();
        break;
      case "analytics":
        await this.loadAnalyticsData();
        break;
    }
  }

async loadUsersData() {
  try {
    const response = await fetch('api/getUsers.php', { credentials: 'same-origin' });
    if (!response.ok) throw new Error(`HTTP ${response.status}`);
    const data = await response.json();

    if (data.success && Array.isArray(data.users)) {
      const normalizedUsers = data.users.map((u) => ({
        id: u.id,
        name: u.name,
        email: u.email,
        role: u.role,
        status: u.status,
        joinDate: u.joinDate || u.join_date || u.joined_at || null,
        avatar: u.avatar || 'https://via.placeholder.com/40/06b6d4/ffffff?text=U',
      }));
      this.dashboardView.updateUsersTable(normalizedUsers);
    } else {
      console.error('Error fetching users:', data.error);
    }
  } catch (err) {
    console.error('Fetch error:', err);
  }
}


  async loadQuestionsData() {
    try {
      const questions = await this.adminModel.getQuestions();
      this.dashboardView.updateQuestionsGrid(questions);
    } catch (error) {
      MediQA.showNotification("Failed to load questions data", "error");
    }
  }

  async loadAnalyticsData() {
    try {
      const analytics = await this.adminModel.getAnalytics();
      this.dashboardView.updateAnalytics(analytics);
    } catch (error) {
      MediQA.showNotification("Failed to load analytics data", "error");
    }
  }

  handleSearch(query) {
    if (query.length < 2) return;

    // Search functionality based on current section
    switch (this.currentSection) {
      case "users":
        this.searchUsers(query);
        break;
      case "questions":
        this.searchQuestions(query);
        break;
    }
  }

  async searchUsers(query) {
    try {
      const results = await this.adminModel.searchUsers(query);
      this.dashboardView.updateUsersTable(results);
    } catch (error) {
      MediQA.showNotification("Search failed", "error");
    }
  }

  async searchQuestions(query) {
    try {
      const results = await this.adminModel.searchQuestions(query);
      this.dashboardView.updateQuestionsGrid(results);
    } catch (error) {
      MediQA.showNotification("Search failed", "error");
    }
  }

  showNotifications() {
    // Show notifications modal or dropdown
    const notifications = this.adminModel.getNotifications();
    this.dashboardView.showNotificationsModal(notifications);
  }
}

/**
 * Admin Model
 * Handles admin data operations
 */
class AdminModel {
  constructor() {
    this.dummyData = this.generateDummyData();
  }

  generateDummyData() {
    return {
      stats: {
        totalUsers: 1234,
        pendingQuestions: 45,
        answersProvided: 2156,
        accuracyRate: 95.2,
      },
      users: [
        {
          id: "user-001",
          name: "Dr. Sarah Johnson",
          email: "sarah.johnson@hospital.com",
          role: "admin",
          status: "active",
          joinDate: "2024-01-15",
          avatar: "https://via.placeholder.com/40/2563eb/ffffff?text=SA",
        },
        {
          id: "user-002",
          name: "Dr. Michael Chen",
          email: "michael.chen@clinic.com",
          role: "provider",
          status: "active",
          joinDate: "2024-01-20",
          avatar: "https://via.placeholder.com/40/059669/ffffff?text=MC",
        },
        {
          id: "user-003",
          name: "Emily Rodriguez",
          email: "emily.rodriguez@email.com",
          role: "patient",
          status: "active",
          joinDate: "2024-01-25",
          avatar: "https://via.placeholder.com/40/06b6d4/ffffff?text=ER",
        },
      ],
      questions: [
        {
          id: "q-001",
          title: "Persistent headache for 3 days",
          category: "Symptoms",
          status: "pending",
          date: "2 hours ago",
          preview:
            "I've been experiencing a constant headache for the past 3 days...",
        },
        {
          id: "q-002",
          title: "Medication side effects",
          category: "Treatments",
          status: "answered",
          date: "1 day ago",
          preview: "I started taking a new medication last week...",
        },
      ],
      activity: [
        {
          type: "user_register",
          text: "New user registered",
          meta: "John Smith • 5 minutes ago",
          icon: "fas fa-user-plus",
        },
        {
          type: "question_submit",
          text: "New question submitted",
          meta: '"Headache symptoms" • 12 minutes ago',
          icon: "fas fa-question",
        },
        {
          type: "answer_provided",
          text: "Answer provided",
          meta: "Dr. Chen • 18 minutes ago",
          icon: "fas fa-comment",
        },
        {
          type: "feedback",
          text: "Answer rated helpful",
          meta: "Patient feedback • 25 minutes ago",
          icon: "fas fa-star",
        },
      ],
    };
  }

  async getDashboardStats() {
    await this.simulateDelay();
    return this.dummyData.stats;
  }

  async getUsers() {
    await this.simulateDelay();
    return this.dummyData.users;
  }

  async getQuestions() {
    await this.simulateDelay();
    return this.dummyData.questions;
  }

  async getRecentActivity() {
    await this.simulateDelay();
    return this.dummyData.activity;
  }

  async getAnalytics() {
    await this.simulateDelay();
    return {
      userGrowth: [100, 120, 150, 180, 200, 250, 300],
      questionVolume: [50, 65, 80, 75, 90, 85, 100],
      responseTime: [2.5, 2.3, 2.1, 1.9, 1.8, 1.7, 1.6],
    };
  }

  async searchUsers(query) {
    await this.simulateDelay();
    return this.dummyData.users.filter(
      (user) =>
        user.name.toLowerCase().includes(query.toLowerCase()) ||
        user.email.toLowerCase().includes(query.toLowerCase())
    );
  }

  async searchQuestions(query) {
    await this.simulateDelay();
    return this.dummyData.questions.filter(
      (question) =>
        question.title.toLowerCase().includes(query.toLowerCase()) ||
        question.preview.toLowerCase().includes(query.toLowerCase())
    );
  }

  getNotifications() {
    return [
      {
        id: 1,
        title: "New user registration",
        message: "John Smith has registered as a patient",
        time: "5 minutes ago",
        unread: true,
      },
      {
        id: 2,
        title: "Question requires attention",
        message: "Urgent question about chest pain needs immediate review",
        time: "12 minutes ago",
        unread: true,
      },
      {
        id: 3,
        title: "System maintenance",
        message: "Scheduled maintenance completed successfully",
        time: "1 hour ago",
        unread: false,
      },
    ];
  }

  simulateDelay() {
    return new Promise((resolve) => setTimeout(resolve, 500));
  }
}

/**
 * Dashboard View
 * Handles dashboard UI updates and interactions
 */
class DashboardView {
  constructor() {
    this.init();
  }

  init() {
    this.initializeCharts();
  }

  updateStats(stats) {
    // Update stat cards with animated counters
    const statCards = document.querySelectorAll(".stat-card");
    statCards.forEach((card, index) => {
      const numberElement = card.querySelector(".stat-number");
      if (numberElement) {
        this.animateCounter(numberElement, Object.values(stats)[index], index);
      }
    });
  }

  animateCounter(element, target, index) {
    let current = 0;
    const increment = target / 50;
    const timer = setInterval(() => {
      current += increment;
      if (current >= target) {
        current = target;
        clearInterval(timer);
      }

      if (index === 3) {
        // Accuracy rate
        element.textContent = current.toFixed(1) + "%";
      } else {
        element.textContent = Math.floor(current).toLocaleString();
      }
    }, 30);
  }

  updateRecentActivity(activities) {
    const activityList = document.querySelector(".activity-list");
    if (activityList) {
      activityList.innerHTML = activities
        .map(
          (activity) => `
                <div class="activity-item">
                    <div class="activity-icon">
                        <i class="${activity.icon}"></i>
                    </div>
                    <div class="activity-content">
                        <div class="activity-text">${activity.text}</div>
                        <div class="activity-meta">${activity.meta}</div>
                    </div>
                </div>
            `
        )
        .join("");
    }
  }

  updateUsersTable(users) {
    const tableBody = document.querySelector("#users-section .table tbody");
    if (tableBody) {
      tableBody.innerHTML = users
        .map(
          (user) => `
                <tr>
                    <td>
                        <div class="user-cell">
                            <img src="${
                              user.avatar
                            }" alt="Avatar" class="user-avatar-small">
                            <div>
                                <div class="user-name">${user.name}</div>
                                <div class="user-email">${user.email}</div>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge badge-${
                      user.role === "admin"
                        ? "info"
                        : user.role === "provider"
                        ? "warning"
                        : "success"
                    }">${user.role}</span></td>
                    <td><span class="badge badge-${
                      user.status === "active" ? "success" : "danger"
                    }">${user.status}</span></td>
                    <td>${this.formatDate(user.joinDate)}</td>
                    <td>
                        <button class="btn btn-small btn-outline" onclick="editUser('${
                          user.id
                        }')">Edit</button>
                        <button class="btn btn-small btn-danger" onclick="suspendUser('${
                          user.id
                        }')">Suspend</button>
                    </td>
                </tr>
            `
        )
        .join("");
    }
  }

  updateQuestionsGrid(questions) {
    const questionsGrid = document.querySelector(".questions-grid");
    if (questionsGrid) {
      questionsGrid.innerHTML = questions
        .map(
          (question) => `
                <div class="question-card">
                    <div class="question-header">
                        <h4>${question.title}</h4>
                        <span class="badge badge-${
                          question.status === "pending" ? "warning" : "success"
                        }">${question.status}</span>
                    </div>
                    <div class="question-meta">
                        <span class="question-category">${
                          question.category
                        }</span>
                        <span class="question-date">${question.date}</span>
                    </div>
                    <p class="question-preview">${question.preview}</p>
                    <div class="question-actions">
                        <button class="btn btn-small btn-primary" onclick="assignQuestion('${
                          question.id
                        }')">Assign to Provider</button>
                        <button class="btn btn-small btn-outline" onclick="viewQuestion('${
                          question.id
                        }')">View Details</button>
                    </div>
                </div>
            `
        )
        .join("");
    }
  }

  showNotificationsModal(notifications) {
    // Create and show notifications modal
    const modal = document.createElement("div");
    modal.className = "modal-overlay active";
    modal.innerHTML = `
            <div class="modal">
                <div class="modal-header">
                    <h3 class="modal-title">Notifications</h3>
                    <button class="modal-close" onclick="this.closest('.modal-overlay').remove()">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="notifications-list">
                        ${notifications
                          .map(
                            (notification) => `
                            <div class="notification-item ${
                              notification.unread ? "unread" : ""
                            }">
                                <div class="notification-content">
                                    <h4>${notification.title}</h4>
                                    <p>${notification.message}</p>
                                    <small>${notification.time}</small>
                                </div>
                            </div>
                        `
                          )
                          .join("")}
                    </div>
                </div>
            </div>
        `;
    document.body.appendChild(modal);
  }

  formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString("en-US", {
      year: "numeric",
      month: "short",
      day: "numeric",
    });
  }

  initializeCharts() {
    // Initialize chart placeholders
    // In a real application, you would integrate with Chart.js or similar
    const chartPlaceholder = document.querySelector(".chart-placeholder");
    if (chartPlaceholder) {
      // Add some interactive elements
      chartPlaceholder.addEventListener("click", () => {
        MediQA.showNotification(
          "Chart functionality would be implemented here",
          "info"
        );
      });
    }
  }
}

// Global functions for admin actions
window.editUser = function (userId) {
  MediQA.showNotification(
    `Edit user ${userId} functionality would be implemented here`,
    "info"
  );
};

window.suspendUser = function (userId) {
  if (confirm("Are you sure you want to suspend this user?")) {
    MediQA.showNotification(`User ${userId} suspended`, "success");
  }
};

window.assignQuestion = function (questionId) {
  MediQA.showNotification(
    `Assign question ${questionId} functionality would be implemented here`,
    "info"
  );
};

window.viewQuestion = function (questionId) {
  MediQA.showNotification(
    `View question ${questionId} functionality would be implemented here`,
    "info"
  );
};

// Initialize admin controller when DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
  // Initialize on admin dashboard page rendered via PHP route
  if (document.body && document.body.classList.contains("dashboard-body")) {
    new AdminController();
  }
});


