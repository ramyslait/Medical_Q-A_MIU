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
    this.dashboardView.initUserActivityChart();

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
    // Load user count
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

    // Load question count
    this.loadQuestionsCount();

    // Load dashboard stats (answers count, accuracy rate, recent activity)
    try {
      const response = await fetch('api/getDashboardStats.php', { credentials: 'same-origin' });
      if (!response.ok) throw new Error(`HTTP ${response.status}`);
      const data = await response.json();

      if (data.success) {
        // Update answers provided stat
        const answersElement = document.getElementById('answersProvidedCount');
        if (answersElement) {
          answersElement.textContent = data.stats.answersProvided.toLocaleString();
        }

        // Update accuracy rate stat
        const accuracyElement = document.getElementById('accuracyRate');
        if (accuracyElement) {
          accuracyElement.textContent = data.stats.accuracyRate + '%';
        }

        // Update stat change indicators (simplified)
        const answersChangeElement = document.getElementById('answersChange');
        if (answersChangeElement) {
          answersChangeElement.textContent = data.stats.answersProvided > 0 ? 
            `${data.stats.answersProvided} total answers` : 'No answers yet';
        }

        const accuracyChangeElement = document.getElementById('accuracyChange');
        if (accuracyChangeElement) {
          accuracyChangeElement.textContent = data.stats.accuracyRate > 0 ? 
            `${data.stats.accuracyRate}% approved` : 'No data';
        }

        // Update recent activity
        if (data.recentActivity && data.recentActivity.length > 0) {
          this.dashboardView.updateRecentActivity(data.recentActivity);
        }
      }
    } catch (error) {
      console.error('Failed to load dashboard stats:', error);
    }
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
      case "answers":
        await this.loadAnswersData();
        break;
      case "forum":
        await this.loadForumData();
        break;
      case "analytics":
        await this.loadAnalyticsData();
        break;
      case "settings":
        await this.loadSettingsData();
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

  async loadAnswersData() {
    try {
      const answers = await this.adminModel.getAnswers();
      this.dashboardView.updateAnswersGrid(answers);
      
      // Bind filter events
      const statusFilter = document.getElementById('answerStatusFilter');
      const categoryFilter = document.getElementById('answerCategoryFilter');
      
      if (statusFilter) {
        statusFilter.addEventListener('change', () => this.filterAnswers());
      }
      if (categoryFilter) {
        categoryFilter.addEventListener('change', () => this.filterAnswers());
      }
    } catch (error) {
      MediQA.showNotification("Failed to load answers data", "error");
    }
  }

  async filterAnswers() {
    try {
      const statusFilter = document.getElementById('answerStatusFilter')?.value || '';
      const categoryFilter = document.getElementById('answerCategoryFilter')?.value || '';
      const answers = await this.adminModel.getAnswers(statusFilter, categoryFilter);
      this.dashboardView.updateAnswersGrid(answers);
    } catch (error) {
      console.error('Error filtering answers:', error);
    }
  }

async loadForumData() {
  try {
    console.log('🔄 [loadForumData] Starting to load forum data...');
    const forumData = await this.adminModel.getForumData();
    console.log('📦 [loadForumData] Forum data received:', forumData);
    console.log('📊 [loadForumData] Stats object:', forumData.stats);
    
    // Specifically check what values are in stats
    console.log('🔍 [loadForumData] Stats breakdown:');
    console.log('   - total:', forumData.stats.total);
    console.log('   - answered (approved):', forumData.stats.answered);
    console.log('   - pending:', forumData.stats.pending);
    console.log('   - not_approved:', forumData.stats.not_approved);
    console.log('   - categories:', forumData.stats.categories);
    
    this.dashboardView.updateForumStats(forumData.stats);
    this.dashboardView.updateForumGrid(forumData.questions);
    
    // Bind filter events
    const statusFilter = document.getElementById('forumStatusFilter');
    const categoryFilter = document.getElementById('forumCategoryFilter');
    const sortFilter = document.getElementById('forumSortFilter');
    
    if (statusFilter) {
      console.log('✅ [loadForumData] Status filter element found');
      statusFilter.addEventListener('change', () => this.filterForum());
    }
    if (categoryFilter) {
      categoryFilter.addEventListener('change', () => this.filterForum());
    }
    if (sortFilter) {
      sortFilter.addEventListener('change', () => this.filterForum());
    }
  } catch (error) {
    console.error('❌ [loadForumData] Failed to load forum data:', error);
    MediQA.showNotification("Failed to load forum data", "error");
  }
}

async filterForum() {
  try {
    // IMPORTANT: Now filtering by doctor_approval_status, not status
    const statusFilter = document.getElementById('forumStatusFilter')?.value || '';
    const categoryFilter = document.getElementById('forumCategoryFilter')?.value || '';
    const sortFilter = document.getElementById('forumSortFilter')?.value || 'recent';
    
    // Update dropdown options to show doctor_approval_status values
    const forumData = await this.adminModel.getForumData(statusFilter, categoryFilter, sortFilter);
    this.dashboardView.updateForumGrid(forumData.questions);
  } catch (error) {
    console.error('Error filtering forum:', error);
  }
}

  async loadQuestionsCount() {
    try {
      const counts = await this.adminModel.getQuestionsCount();
      this.dashboardView.updateQuestionCount(counts);
    } catch (error) {
      console.error("Failed to load question counts:", error);
    }
  }

  async loadAnalyticsData() {
    try {
      const analytics = await this.adminModel.getAnalytics();
      this.dashboardView.updateAnalytics(analytics);
    } catch (error) {
      console.error('Failed to load analytics:', error);
      MediQA.showNotification("Failed to load analytics data", "error");
    }
  }

  async loadSettingsData() {
    try {
      // Load settings from localStorage or use defaults
      const settings = this.adminModel.getSettings();
      this.dashboardView.updateSettings(settings);
    } catch (error) {
      console.error('Failed to load settings:', error);
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
      case "answers":
        this.searchAnswers(query);
        break;
      case "forum":
        this.searchForum(query);
        break;
    }
  }

  async searchAnswers(query) {
    try {
      const answers = await this.adminModel.getAnswers();
      const filtered = answers.filter(
        (answer) =>
          answer.title.toLowerCase().includes(query.toLowerCase()) ||
          answer.body.toLowerCase().includes(query.toLowerCase()) ||
          (answer.ai_answer && answer.ai_answer.toLowerCase().includes(query.toLowerCase())) ||
          (answer.user_name && answer.user_name.toLowerCase().includes(query.toLowerCase()))
      );
      this.dashboardView.updateAnswersGrid(filtered);
    } catch (error) {
      MediQA.showNotification("Search failed", "error");
    }
  }

async searchForum(query) {
  try {
    const forumData = await this.adminModel.getForumData();
    const filtered = forumData.questions.filter(
      (question) =>
        question.title.toLowerCase().includes(query.toLowerCase()) ||
        question.body.toLowerCase().includes(query.toLowerCase()) ||
        (question.user_name && question.user_name.toLowerCase().includes(query.toLowerCase())) ||
        (question.category && question.category.toLowerCase().includes(query.toLowerCase())) ||
        (question.doctor_approval_status && question.doctor_approval_status.toLowerCase().includes(query.toLowerCase()))
    );
    this.dashboardView.updateForumGrid(filtered);
  } catch (error) {
    MediQA.showNotification("Search failed", "error");
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
      const questions = await this.adminModel.getQuestions();
      const filtered = questions.filter(
        (question) =>
          question.title.toLowerCase().includes(query.toLowerCase()) ||
          question.body.toLowerCase().includes(query.toLowerCase()) ||
          (question.user_name && question.user_name.toLowerCase().includes(query.toLowerCase()))
      );
      this.dashboardView.updateQuestionsGrid(filtered);
    } catch (error) {
      MediQA.showNotification("Search failed", "error");
    }
  }

  showNotifications() {
    // Show notifications modal or dropdown
    const notifications = this.adminModel.getNotifications();
    this.dashboardView.showNotificationsModal(notifications);
  }

  // Question management functions
  assignQuestion(questionId) {
    // TODO: Implement assign question functionality
    MediQA.showNotification(`Assigning question ${questionId} to provider...`, "info");
  }

  async viewQuestion(questionId) {
    try {
      const response = await fetch(`api/getQuestionById.php?id=${questionId}`, { credentials: 'same-origin' });
      if (!response.ok) throw new Error(`HTTP ${response.status}`);
      const data = await response.json();

      if (data.success && data.question) {
        this.dashboardView.showQuestionDetailsModal(data.question);
      } else {
        MediQA.showNotification(data.error || 'Failed to load question details', "error");
      }
    } catch (error) {
      console.error('Error fetching question:', error);
      MediQA.showNotification('Failed to load question details', "error");
    }
  }

  markResolved(questionId) {
    // TODO: Implement mark question as resolved functionality
    MediQA.showNotification(`Marking question ${questionId} as resolved...`, "info");
  }
}

// Make question management functions globally available
let adminControllerInstance = null;

// Initialize admin controller when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
  adminControllerInstance = new AdminController();
});

// Global functions for HTML onclick handlers
function assignQuestion(questionId) {
  if (adminControllerInstance) {
    adminControllerInstance.assignQuestion(questionId);
  }
}

function viewQuestion(questionId) {
  if (adminControllerInstance) {
    adminControllerInstance.viewQuestion(questionId);
  }
}

function markResolved(questionId) {
  if (adminControllerInstance) {
    adminControllerInstance.markResolved(questionId);
  }
}

function saveSettings(category) {
  if (!adminControllerInstance) return;

  const settings = {};
  
  if (category === 'general') {
    settings.siteName = document.getElementById('siteName')?.value || '';
    settings.siteDescription = document.getElementById('siteDescription')?.value || '';
    settings.questionsPerPage = parseInt(document.getElementById('questionsPerPage')?.value) || 20;
    settings.defaultSortOrder = document.getElementById('defaultSortOrder')?.value || 'recent';
  } else if (category === 'notifications') {
    settings.emailNewQuestion = document.getElementById('emailNewQuestion')?.checked || false;
    settings.emailNewUser = document.getElementById('emailNewUser')?.checked || false;
    settings.emailDailyReport = document.getElementById('emailDailyReport')?.checked || false;
    settings.reportFrequency = document.getElementById('reportFrequency')?.value || 'weekly';
  } else if (category === 'security') {
    settings.minPasswordLength = parseInt(document.getElementById('minPasswordLength')?.value) || 8;
    settings.requireUppercase = document.getElementById('requireUppercase')?.checked || false;
    settings.requireNumbers = document.getElementById('requireNumbers')?.checked || false;
    settings.requireSpecialChars = document.getElementById('requireSpecialChars')?.checked || false;
    settings.sessionTimeout = parseInt(document.getElementById('sessionTimeout')?.value) || 30;
  } else if (category === 'ai') {
    settings.aiProvider = document.getElementById('aiProvider')?.value || 'deepseek';
    settings.maxTokens = parseInt(document.getElementById('maxTokens')?.value) || 1000;
    settings.temperature = parseFloat(document.getElementById('temperature')?.value) || 0.7;
    settings.autoGenerateAnswers = document.getElementById('autoGenerateAnswers')?.checked !== false;
    settings.requireReview = document.getElementById('requireReview')?.checked || false;
  }

  const success = adminControllerInstance.adminModel.saveSettings(category, settings);
  if (success) {
    MediQA.showNotification(`${category.charAt(0).toUpperCase() + category.slice(1)} settings saved successfully`, "success");
  } else {
    MediQA.showNotification("Failed to save settings", "error");
  }
}

function resetSettings(category) {
  if (!adminControllerInstance) return;
  
  if (confirm(`Are you sure you want to reset ${category} settings to defaults?`)) {
    const defaultSettings = adminControllerInstance.adminModel.getSettings();
    adminControllerInstance.dashboardView.updateSettings(defaultSettings);
    MediQA.showNotification(`${category.charAt(0).toUpperCase() + category.slice(1)} settings reset to defaults`, "info");
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
    try {
      const response = await fetch('api/getQuestions.php', { credentials: 'same-origin' });
      if (!response.ok) throw new Error(`HTTP ${response.status}`);
      const data = await response.json();
      
      if (data.success) {
        return data.questions;
      } else {
        throw new Error(data.error || 'Failed to fetch questions');
      }
    } catch (error) {
      console.error('Error fetching questions:', error);
      throw error;
    }
  }

  async getQuestionsCount() {
    try {
      const response = await fetch('api/getQuestionsCount.php', { credentials: 'same-origin' });
      if (!response.ok) throw new Error(`HTTP ${response.status}`);
      const data = await response.json();
      
      if (data.success) {
        return data.counts;
      } else {
        throw new Error(data.error || 'Failed to fetch question counts');
      }
    } catch (error) {
      console.error('Error fetching question counts:', error);
      return { total: 0, pending: 0, answered: 0, closed: 0 };
    }
  }

  async getAnswers(statusFilter = '', categoryFilter = '') {
    try {
      let url = 'api/getQuestions.php';
      const params = new URLSearchParams();
      if (statusFilter) params.append('doctor_approval_status', statusFilter);
      if (categoryFilter) params.append('category', categoryFilter);
      if (params.toString()) url += '?' + params.toString();
      
      const response = await fetch(url, { credentials: 'same-origin' });
      if (!response.ok) throw new Error(`HTTP ${response.status}`);
      const data = await response.json();
      
      if (data.success) {
        // Filter to only show questions with AI answers
        return data.questions.filter(q => q.ai_answer && q.ai_answer.trim() !== '');
      } else {
        throw new Error(data.error || 'Failed to fetch answers');
      }
    } catch (error) {
      console.error('Error fetching answers:', error);
      throw error;
    }
  }

async getForumData(statusFilter = '', categoryFilter = '', sortBy = 'recent') {
  try {
    let url = 'api/getQuestions.php';
    const params = new URLSearchParams();
    
    // Filter by doctor_approval_status, not status
    if (statusFilter && statusFilter !== '' && statusFilter !== 'all') {
      params.append('doctor_approval_status', statusFilter);
    }
    
    if (categoryFilter && categoryFilter !== '' && categoryFilter !== 'all') {
      params.append('category', categoryFilter);
    }
    
    if (params.toString()) url += '?' + params.toString();
    
    const response = await fetch(url, { credentials: 'same-origin' });
    if (!response.ok) throw new Error(`HTTP ${response.status}`);
    const data = await response.json();
    
    if (data.success) {
      let questions = data.questions;
      
      // Sort questions
      switch (sortBy) {
        case 'oldest':
          questions.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
          break;
        case 'category':
          questions.sort((a, b) => (a.category || '').localeCompare(b.category || ''));
          break;
        case 'status':
          // Sort by doctor_approval_status
          questions.sort((a, b) => (a.doctor_approval_status || '').localeCompare(b.doctor_approval_status || ''));
          break;
        case 'recent':
        default:
          questions.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
          break;
      }
      
      // Count by doctor_approval_status, not status
      const stats = {
        total: questions.length,
        pending: questions.filter(q => !q.doctor_approval_status || q.doctor_approval_status === 'pending').length,
        answered: questions.filter(q => q.doctor_approval_status === 'approved').length,
        not_approved: questions.filter(q => q.doctor_approval_status === 'not_approved').length,
        categories: new Set(questions.map(q => q.category).filter(Boolean)).size
      };
      
      return { questions, stats };
    } else {
      throw new Error(data.error || 'Failed to fetch forum data');
    }
  } catch (error) {
    console.error('Error fetching forum data:', error);
    throw error;
  }
}

  async getRecentActivity() {
    await this.simulateDelay();
    return this.dummyData.activity;
  }

  async getAnalytics() {
    try {
      const response = await fetch('api/getAnalytics.php', { credentials: 'same-origin' });
      if (!response.ok) throw new Error(`HTTP ${response.status}`);
      const data = await response.json();
      
      if (data.success) {
        return data.analytics;
      } else {
        throw new Error(data.error || 'Failed to fetch analytics');
      }
    } catch (error) {
      console.error('Error fetching analytics:', error);
      throw error;
    }
  }

  getSettings() {
    // Load settings from localStorage or return defaults
    const defaultSettings = {
      general: {
        siteName: 'Medical Q&A',
        siteDescription: 'Medical Q&A Platform for Community Health Support',
        questionsPerPage: 20,
        defaultSortOrder: 'recent'
      },
      notifications: {
        emailNewQuestion: true,
        emailNewUser: true,
        emailDailyReport: false,
        reportFrequency: 'weekly'
      },
      security: {
        minPasswordLength: 8,
        requireUppercase: false,
        requireNumbers: false,
        requireSpecialChars: false,
        sessionTimeout: 30
      },
      ai: {
        aiProvider: 'deepseek',
        maxTokens: 1000,
        temperature: 0.7,
        autoGenerateAnswers: true,
        requireReview: false
      }
    };

    try {
      const stored = localStorage.getItem('adminSettings');
      if (stored) {
        return JSON.parse(stored);
      }
    } catch (e) {
      console.error('Error loading settings:', e);
    }

    return defaultSettings;
  }

  saveSettings(category, settings) {
    try {
      const allSettings = this.getSettings();
      allSettings[category] = { ...allSettings[category], ...settings };
      localStorage.setItem('adminSettings', JSON.stringify(allSettings));
      return true;
    } catch (e) {
      console.error('Error saving settings:', e);
      return false;
    }
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
          <tr data-user-id="${user.id}">
            <td>
              <div class="user-cell">
                <div>
                  <div class="user-name">${user.name}</div>
                  <div class="user-email">${user.email}</div>
                </div>
              </div>
            </td>
            <td>
              <span class="badge badge-${
                user.role === "admin"
                  ? "info"
                  : user.role === "doctor"
                  ? "warning"
                  : "success"
              }">
                ${user.role}
              </span>
            </td>
            <td>
              <span class="badge badge-${
                user.status === "active" || user.status === "verified" ? "success" : "danger"
              }">
                ${user.status === "active" || user.status === "verified" ? "Active" : "Suspended"}
              </span>
            </td>
            <td>${this.formatDate(user.joinDate)}</td>
            <td>
              <button class="btn btn-small btn-outline" onclick="editUser('${user.id}')">
                Edit
              </button>
              ${
                user.status === "active" || user.status === "verified"
                  ? `<button class="btn btn-small btn-danger" onclick="suspendUser('${user.id}', '${user.name}')">
                      Suspend
                    </button>`
                  : `<button class="btn btn-small btn-success" onclick="activateUser('${user.id}', '${user.name}')">
                      Activate
                    </button>`
              }
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
                        !question.doctor_approval_status || question.doctor_approval_status === "pending" ? "warning" : 
                        question.doctor_approval_status === "approved" ? "success" : 
                        question.doctor_approval_status === "not_approved" ? "danger" : "secondary"
                      }">${(question.doctor_approval_status || 'pending').charAt(0).toUpperCase() + (question.doctor_approval_status || 'pending').slice(1)}</span>
                    </div>
                    <div class="question-meta">
                        <span class="question-category">${
                          question.category.charAt(0).toUpperCase() + question.category.slice(1)
                        }</span>
                        <span class="question-date">${question.time_ago}</span>
                    </div>
                    <p class="question-preview">${question.preview}</p>
                    <div class="question-user-info">
                        <small>Asked by: ${question.user_name || 'Anonymous'}</small>
                    </div>
                    <div class="question-actions">
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

  updateQuestionCount(counts) {
    // Update sidebar badge
    const questionCountElement = document.getElementById('questionCount');
    if (questionCountElement) {
      questionCountElement.textContent = counts.total;
      
      // Update badge color based on pending questions
      questionCountElement.className = 'badge ' + 
        (counts.pending > 0 ? 'badge-warning' : 'badge-success');
    }

    // Update stat card
    const pendingCountElement = document.getElementById('pendingQuestionsCount');
    if (pendingCountElement) {
      pendingCountElement.textContent = counts.pending;
    }

    // Update stat change (simplified - in real app you'd calculate daily change)
    const pendingChangeElement = document.getElementById('pendingQuestionsChange');
    if (pendingChangeElement) {
      const changeText = counts.pending > 0 ? 
        `+${counts.pending} pending` : 
        'All answered!';
      pendingChangeElement.textContent = changeText;
    }
  }

  updateAnswersGrid(answers) {
    const answersGrid = document.querySelector(".answers-grid");
    if (answersGrid) {
      if (answers.length === 0) {
        answersGrid.innerHTML = `
          <div class="empty-state">
            <i class="fas fa-comments" style="font-size: 3rem; color: #9ca3af; margin-bottom: 1rem;"></i>
            <p>No answers found</p>
          </div>
        `;
        return;
      }

      answersGrid.innerHTML = answers
        .map(
          (answer) => `
                <div class="answer-card">
                    <div class="answer-header">
                        <h4>${this.escapeHtml(answer.title)}</h4>
                        <span class="badge badge-${
                          answer.doctor_approval_status === "pending" ? "warning" : 
                          answer.doctor_approval_status === "approved" ? "success" : "secondary"
                        }">${answer.doctor_approval_status.charAt(0).toUpperCase() + answer.doctor_approval_status.slice(1)}</span>
                    </div>
                    <div class="answer-meta">
                        <span class="answer-category badge badge-info">${
                          this.escapeHtml(answer.category || 'N/A')
                        }</span>
                        <span class="answer-date">${answer.time_ago}</span>
                    </div>
                    <div class="answer-question">
                        <strong>Question:</strong>
                        <p>${this.escapeHtml(answer.body.substring(0, 200))}${answer.body.length > 200 ? '...' : ''}</p>
                    </div>
                    <div class="answer-content">
                        <strong>AI Answer:</strong>
                        <p>${this.escapeHtml(answer.ai_answer.substring(0, 300))}${answer.ai_answer.length > 300 ? '...' : ''}</p>
                    </div>
                    <div class="answer-user-info">
                        <small>Asked by: ${this.escapeHtml(answer.user_name || 'Anonymous')}</small>
                    </div>
                    <div class="answer-actions">
                        <button class="btn btn-small btn-outline" onclick="viewQuestion('${
                          answer.id
                        }')">View Full Details</button>
                        
                    </div>
                </div>
            `
        )
        .join("");
    }
  }

updateForumStats(stats) {
  console.log('📊 updateForumStats called with:', stats);
  
  const totalElement = document.getElementById('forumTotalQuestions');
  const answeredElement = document.getElementById('forumAnsweredQuestions');
  const pendingElement = document.getElementById('forumPendingQuestions');
  const categoriesElement = document.getElementById('forumCategories');

  if (totalElement) {
    totalElement.textContent = (stats.total || 0).toLocaleString();
  }
  
  if (answeredElement) {
    answeredElement.textContent = (stats.answered || 0).toLocaleString();
  }
  
  if (pendingElement) {
    pendingElement.textContent = (stats.pending || 0).toLocaleString();
    
    // Highlight if pending
    if ((stats.pending || 0) > 0) {
      pendingElement.style.color = '#f59e0b';
      pendingElement.style.fontWeight = 'bold';
    } else {
      pendingElement.style.color = '';
      pendingElement.style.fontWeight = '';
    }
  }
  
  if (categoriesElement) {
    categoriesElement.textContent = (stats.categories || 0).toLocaleString();
  }
}
updateForumGrid(questions) {
  const forumGrid = document.querySelector(".forum-management-grid");
  if (forumGrid) {
    if (questions.length === 0) {
      forumGrid.innerHTML = `<div class="empty-state">No forum discussions found</div>`;
      return;
    }

    forumGrid.innerHTML = questions
      .map(
        (question) => `
              <div class="forum-discussion-card">
                  <div class="forum-discussion-header">
                      <div class="forum-discussion-title-section">
                          <h4>${this.escapeHtml(question.title)}</h4>
                          <div class="forum-discussion-meta">
                              <!-- Show doctor_approval_status instead of status -->
                              <span class="badge badge-${
                                question.doctor_approval_status === "pending" ? "warning" : 
                                question.doctor_approval_status === "approved" ? "success" : "secondary"
                              }">${(question.doctor_approval_status || 'pending').charAt(0).toUpperCase() + (question.doctor_approval_status || 'pending').slice(1)}</span>
                              <span class="badge badge-info">${this.escapeHtml(question.category || 'N/A')}</span>
                              <span class="forum-date">${question.time_ago}</span>
                          </div>
                      </div>
                  </div>
                  <div class="forum-discussion-content">
                      <p>${this.escapeHtml(question.body.substring(0, 150))}${question.body.length > 150 ? '...' : ''}</p>
                  </div>
                  <div class="forum-discussion-info">
                      <div class="forum-user-info">
                          <i class="fas fa-user"></i>
                          <span>${this.escapeHtml(question.user_name || 'Anonymous')}</span>
                      </div>
                      <div class="forum-answer-status">
                          ${question.doctor_approval_status === 'approved' ? 
                            '<i class="fas fa-check-circle" style="color: #10b981;"></i> <span>Approved</span>' : 
                            question.doctor_approval_status === 'not_approved' ?
                            '<i class="fas fa-times-circle" style="color: #ef4444;"></i> <span>Rejected</span>' :
                            '<i class="fas fa-clock" style="color: #f59e0b;"></i> <span>Pending Review</span>'
                          }
                      </div>
                  </div>
                  <div class="forum-discussion-actions">
                      <button class="btn btn-small btn-outline" onclick="viewQuestion('${
                        question.id
                      }')">Review</button>
                  </div>
              </div>
          `
      )
      .join("");
  }
}

  showQuestionDetailsModal(question) {
    // Remove any existing modals
    const existingModal = document.querySelector('.modal-overlay');
    if (existingModal) {
      existingModal.remove();
    }

    // Create and show question details modal
    const modal = document.createElement("div");
    modal.className = "modal-overlay active";
    modal.innerHTML = `
            <div class="modal" style="max-width: 800px;">
                <div class="modal-header">
                    <h3 class="modal-title">Question Details</h3>
                    <button class="modal-close" onclick="this.closest('.modal-overlay').remove()">&times;</button>
                </div>
                <div class="modal-body" style="max-height: 80vh; overflow-y: auto;">
                    <div class="question-details">
                        <div class="question-detail-section">
                            <h4>Question Information</h4>
                            <div class="detail-row">
                                <strong>Title:</strong>
                                <span>${this.escapeHtml(question.title)}</span>
                            </div>
                            <div class="detail-row">
                                <strong>Category:</strong>
                                <span class="badge badge-info">${this.escapeHtml(question.category || 'N/A')}</span>
                            </div>
                            <div class="detail-row">
                                <strong>Status:</strong>
                                <span class="badge badge-${
                                  question.status === "pending" ? "warning" : 
                                  question.status === "answered" ? "success" : "secondary"
                                }">${question.status.charAt(0).toUpperCase() + question.status.slice(1)}</span>
                            </div>
                            <div class="detail-row">
                                <strong>Asked by:</strong>
                                <span>${this.escapeHtml(question.user_name || 'Anonymous')} (${this.escapeHtml(question.user_email || 'N/A')})</span>
                            </div>
                            <div class="detail-row">
                                <strong>Date:</strong>
                                <span>${this.formatDate(question.created_at)} (${question.time_ago})</span>
                            </div>
                        </div>

                        <div class="question-detail-section">
                            <h4>Question Description</h4>
                            <div class="question-body">
                                ${this.formatText(question.body)}
                            </div>
                        </div>

                        ${question.ai_answer ? `
                        <div class="question-detail-section">
                            <h4>AI Generated Answer</h4>
                            <div class="ai-answer">
                                ${this.formatText(question.ai_answer)}
                            </div>
                        </div>
                        ` : `
                        <div class="question-detail-section">
                            <h4>AI Generated Answer</h4>
                            <div class="ai-answer no-answer">
                                <p style="color: #999; font-style: italic;">No AI answer available yet.</p>
                            </div>
                        </div>
                        `}
                    </div>
                </div>
                <div class="modal-footer" style="padding: 1rem; border-top: 1px solid #e5e7eb; display: flex; gap: 0.5rem; justify-content: flex-end;">
                    <button class="btn btn-outline" onclick="this.closest('.modal-overlay').remove()">Close</button>
                  
                </div>
            </div>
        `;
    document.body.appendChild(modal);

    // Close modal when clicking outside
    modal.addEventListener('click', (e) => {
      if (e.target === modal) {
        modal.remove();
      }
    });
  }

  escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }

  formatText(text) {
    if (!text) return '';
    // Convert line breaks to <br> tags
    return this.escapeHtml(text).replace(/\n/g, '<br>');
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
    // Charts will be initialized when analytics data is loaded
  }
updateAnalytics(analytics) {
  if (!analytics) return;

  // Update key metrics
  const avgResponseTimeEl = document.getElementById('avgResponseTime');
  if (avgResponseTimeEl) {
    avgResponseTimeEl.textContent = analytics.avgResponseTime 
      ? `${analytics.avgResponseTime} minutes` 
      : 'N/A';
  }

  const topCategoryEl = document.getElementById('topCategory');
  if (topCategoryEl && analytics.topCategories && analytics.topCategories.length > 0) {
    topCategoryEl.textContent = analytics.topCategories[0].category || 'N/A';
  }

  const totalCategoriesEl = document.getElementById('totalCategories');
  if (totalCategoriesEl && analytics.categoryDistribution) {
    totalCategoriesEl.textContent = analytics.categoryDistribution.length;
  }

  // NEW: Update doctor approval metrics
  const approvalRateEl = document.getElementById('approvalRate');
  if (approvalRateEl && analytics.approvalRate !== undefined) {
    approvalRateEl.textContent = analytics.approvalRate + '%';
  }

  // NEW: Update approval stats if available
  if (analytics.approvalStats) {
    const approvedEl = document.getElementById('approvedCount');
    const reviewedEl = document.getElementById('reviewedCount');
    const pendingReviewEl = document.getElementById('pendingReviewCount');
    const totalQuestionsEl = document.getElementById('totalQuestionsCount');
    
    if (approvedEl) approvedEl.textContent = analytics.approvalStats.approved.toLocaleString();
    if (reviewedEl) reviewedEl.textContent = analytics.approvalStats.reviewed.toLocaleString();
    if (pendingReviewEl) pendingReviewEl.textContent = analytics.approvalStats.pending_review.toLocaleString();
    if (totalQuestionsEl) totalQuestionsEl.textContent = analytics.approvalStats.total.toLocaleString();
  }

  // Initialize charts
  this.initUserGrowthChart(analytics.userGrowth);
this.initQuestionVolumeChart(analytics.statusDistribution);
  this.initCategoryDistributionChart(analytics.categoryDistribution);
  this.initStatusDistributionChart(analytics.statusDistribution);
  this.initMonthlyUsersChart(analytics.monthlyUsers);
  this.initMonthlyQuestionsChart(analytics.monthlyQuestions);
}

  initUserGrowthChart(data) {
    const ctx = document.getElementById('userGrowthChart');
    if (!ctx || !data) return;

    const labels = [];
    for (let i = 6; i >= 0; i--) {
      const date = new Date();
      date.setDate(date.getDate() - i);
      labels.push(date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));
    }

    new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'Total Users',
          data: data,
          borderColor: '#2563eb',
          backgroundColor: 'rgba(37, 99, 235, 0.1)',
          tension: 0.4,
          fill: true
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          }
        },
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });
  }

initQuestionVolumeChart(statusDistribution) {
  const ctx = document.getElementById('questionVolumeChart');
  if (!ctx) return;

  // Clear previous chart if exists
  if (ctx.chart) {
    ctx.chart.destroy();
    ctx.chart = null;
  }

  // Extract counts from statusDistribution
  const approvedCount = statusDistribution.find(s => s.status === 'approved')?.count || 0;
  const pendingCount = statusDistribution.find(s => s.status === 'pending')?.count || 0;
  const notApprovedCount = statusDistribution.find(s => s.status === 'not_approved')?.count || 0;

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Approved', 'Pending', 'Not Approved'],
      datasets: [{
        label: 'Questions by Doctor Approval Status',
        data: [approvedCount, pendingCount, notApprovedCount],
        backgroundColor: [
          '#10b981', // Green for approved
          '#f59e0b', // Orange for pending  
          '#ef4444'  // Red for not approved
        ]
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            stepSize: 1
          }
        }
      }
    }
  });
}

// Helper method to fetch question volume by doctor_approval_status
async fetchQuestionVolumeByApprovalStatus() {
  try {
    const approvedData = [];
    const pendingData = [];
    const notApprovedData = [];

    // Fetch data for each day
    for (let i = 6; i >= 0; i--) {
      const date = new Date();
      date.setDate(date.getDate() - i);
      const dateStr = date.toISOString().split('T')[0];

      const response = await fetch(`api/getQuestionsByDate.php?date=${dateStr}`, { 
        credentials: 'same-origin' 
      });
      
      if (response.ok) {
        const data = await response.json();
        if (data.success) {
          approvedData.push(data.approved || 0);
          pendingData.push(data.pending || 0);
          notApprovedData.push(data.not_approved || 0);
        }
      }
    }

    return {
      approved: approvedData,
      pending: pendingData,
      not_approved: notApprovedData
    };
  } catch (error) {
    console.error('Error fetching approval status data:', error);
    throw error;
  }
}

// Fallback method if the new API isn't available
initQuestionVolumeChartFallback(questionData, answerData) {
  const ctx = document.getElementById('questionVolumeChart');
  if (!ctx || !questionData) return;

  const labels = [];
  for (let i = 6; i >= 0; i--) {
    const date = new Date();
    date.setDate(date.getDate() - i);
    labels.push(date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));
  }

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [
        {
          label: 'Questions',
          data: questionData,
          backgroundColor: '#3b82f6'
        },
        {
          label: 'AI Answers',
          data: answerData || [],
          backgroundColor: '#10b981'
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
}

  initCategoryDistributionChart(data) {
    const container = document.getElementById('categoryDistributionChart');
    if (!container || !data || data.length === 0) {
      if (container) container.innerHTML = '<p class="text-muted">No category data available</p>';
      return;
    }

    const ctx = document.createElement('canvas');
    container.innerHTML = '';
    container.appendChild(ctx);

    new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: data.map(d => d.category),
        datasets: [{
          data: data.map(d => d.count),
          backgroundColor: [
            '#2563eb', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'
          ]
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false
      }
    });
  }

  initStatusDistributionChart(data) {
    const container = document.getElementById('statusDistributionChart');
    if (!container || !data || data.length === 0) {
      if (container) container.innerHTML = '<p class="text-muted">No status data available</p>';
      return;
    }

    const ctx = document.createElement('canvas');
    container.innerHTML = '';
    container.appendChild(ctx);

    const statusColors = {
      'pending': '#f59e0b',
      'approved': '#10b981',
      'not_approved': '#6b7280'
    };

    new Chart(ctx, {
      type: 'pie',
      data: {
        labels: data.map(d => d.status.charAt(0).toUpperCase() + d.status.slice(1)),
        datasets: [{
          data: data.map(d => d.count),
          backgroundColor: data.map(d => statusColors[d.status] || '#6b7280')
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false
      }
    });
  }

  initMonthlyUsersChart(data) {
    const ctx = document.getElementById('monthlyUsersChart');
    if (!ctx || !data) return;

    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: data.map(d => d.month),
        datasets: [{
          label: 'New Users',
          data: data.map(d => d.count),
          backgroundColor: '#2563eb'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });
  }

  initMonthlyQuestionsChart(data) {
    const ctx = document.getElementById('monthlyQuestionsChart');
    if (!ctx || !data) return;

    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: data.map(d => d.month),
        datasets: [{
          label: 'Questions',
          data: data.map(d => d.count),
          backgroundColor: '#10b981'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });
  }


  initUserActivityChart() {
  const chartContainer = document.querySelector('.chart-placeholder');
  if (!chartContainer) return;
  
  chartContainer.innerHTML = '<canvas id="userActivityChart"></canvas>';
  const ctx = document.getElementById('userActivityChart');
  if (!ctx) return;
  
  if (ctx.chart) {
    ctx.chart.destroy();
    ctx.chart = null;
  }
  
  new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ['Questions', 'Answers', 'Users', 'Reviews'],
      datasets: [{
        data: [18, 18, 5, 7],
        backgroundColor: [
          '#2563eb',
          '#10b981',
          '#f59e0b',
          '#8b5cf6'
        ]
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom'
        }
      }
    }
  });
}

  updateSettings(settings) {
    if (!settings) return;

    // General settings
    if (settings.general) {
      const siteName = document.getElementById('siteName');
      const siteDescription = document.getElementById('siteDescription');
      const questionsPerPage = document.getElementById('questionsPerPage');
      const defaultSortOrder = document.getElementById('defaultSortOrder');

      if (siteName) siteName.value = settings.general.siteName || '';
      if (siteDescription) siteDescription.value = settings.general.siteDescription || '';
      if (questionsPerPage) questionsPerPage.value = settings.general.questionsPerPage || 20;
      if (defaultSortOrder) defaultSortOrder.value = settings.general.defaultSortOrder || 'recent';
    }

    // Notification settings
    if (settings.notifications) {
      const emailNewQuestion = document.getElementById('emailNewQuestion');
      const emailNewUser = document.getElementById('emailNewUser');
      const emailDailyReport = document.getElementById('emailDailyReport');
      const reportFrequency = document.getElementById('reportFrequency');

      if (emailNewQuestion) emailNewQuestion.checked = settings.notifications.emailNewQuestion || false;
      if (emailNewUser) emailNewUser.checked = settings.notifications.emailNewUser || false;
      if (emailDailyReport) emailDailyReport.checked = settings.notifications.emailDailyReport || false;
      if (reportFrequency) reportFrequency.value = settings.notifications.reportFrequency || 'weekly';
    }

    // Security settings
    if (settings.security) {
      const minPasswordLength = document.getElementById('minPasswordLength');
      const requireUppercase = document.getElementById('requireUppercase');
      const requireNumbers = document.getElementById('requireNumbers');
      const requireSpecialChars = document.getElementById('requireSpecialChars');
      const sessionTimeout = document.getElementById('sessionTimeout');

      if (minPasswordLength) minPasswordLength.value = settings.security.minPasswordLength || 8;
      if (requireUppercase) requireUppercase.checked = settings.security.requireUppercase || false;
      if (requireNumbers) requireNumbers.checked = settings.security.requireNumbers || false;
      if (requireSpecialChars) requireSpecialChars.checked = settings.security.requireSpecialChars || false;
      if (sessionTimeout) sessionTimeout.value = settings.security.sessionTimeout || 30;
    }

    // AI settings
    if (settings.ai) {
      const aiProvider = document.getElementById('aiProvider');
      const maxTokens = document.getElementById('maxTokens');
      const temperature = document.getElementById('temperature');
      const autoGenerateAnswers = document.getElementById('autoGenerateAnswers');
      const requireReview = document.getElementById('requireReview');

      if (aiProvider) aiProvider.value = settings.ai.aiProvider || 'deepseek';
      if (maxTokens) maxTokens.value = settings.ai.maxTokens || 1000;
      if (temperature) temperature.value = settings.ai.temperature || 0.7;
      if (autoGenerateAnswers) autoGenerateAnswers.checked = settings.ai.autoGenerateAnswers !== false;
      if (requireReview) requireReview.checked = settings.ai.requireReview || false;
    }

    // Initialize settings tabs
    this.initSettingsTabs();
  }

  initSettingsTabs() {
    const tabs = document.querySelectorAll('.settings-tab');
    const contents = document.querySelectorAll('.settings-content');

    tabs.forEach(tab => {
      tab.addEventListener('click', () => {
        const targetTab = tab.dataset.tab;

        // Remove active class from all tabs and contents
        tabs.forEach(t => t.classList.remove('active'));
        contents.forEach(c => c.classList.remove('active'));

        // Add active class to clicked tab and corresponding content
        tab.classList.add('active');
        const targetContent = document.getElementById(`${targetTab}-settings`);
        if (targetContent) {
          targetContent.classList.add('active');
        }
      });
    });
  }
}

// Global functions for admin actions
// Global functions for admin actions
window.editUser = async function (userId) {
  // Show edit modal
  const user = await getUserDetails(userId);
  showEditUserModal(user);
};

window.suspendUser = async function (userId) {
  if (confirm("Are you sure you want to suspend this user?")) {
    try {
      const response = await fetch('api/suspendUser.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ user_id: userId }),
        credentials: 'same-origin'
      });
      
      const data = await response.json();
      
      if (data.success) {
        MediQA.showNotification(`User suspended successfully`, "success");
        // Refresh user list
        if (adminControllerInstance) {
          adminControllerInstance.loadUsersData();
        }
      } else {
        MediQA.showNotification(data.error || "Failed to suspend user", "error");
      }
    } catch (error) {
      console.error('Error suspending user:', error);
      MediQA.showNotification("Failed to suspend user", "error");
    }
  }
};

// Helper function to get user details
async function getUserDetails(userId) {
  try {
    const response = await fetch(`api/getUser.php?id=${userId}`, {
      credentials: 'same-origin'
    });
    const data = await response.json();
    return data.success ? data.user : null;
  } catch (error) {
    console.error('Error fetching user details:', error);
    return null;
  }
}

// Function to show edit modal
function showEditUserModal(user) {
  if (!user) return;
  
  // Remove existing modal
  const existingModal = document.querySelector('.modal-overlay');
  if (existingModal) existingModal.remove();
  
  const modal = document.createElement("div");
  modal.className = "modal-overlay active";
  modal.innerHTML = `
    <div class="modal">
      <div class="modal-header">
        <h3 class="modal-title">Edit User: ${user.name}</h3>
        <button class="modal-close" onclick="this.closest('.modal-overlay').remove()">&times;</button>
      </div>
      <div class="modal-body">
        <form id="editUserForm">
          <input type="hidden" name="user_id" value="${user.id}">
          
          <div class="form-group">
            <label class="form-label">Name</label>
            <input type="text" class="form-input" name="name" value="${user.name}" required>
          </div>
          
          <div class="form-group">
            <label class="form-label">Email</label>
            <input type="email" class="form-input" name="email" value="${user.email}" required>
          </div>
          
          <div class="form-group">
            <label class="form-label">Role</label>
            <select class="form-select" name="role">
              <option value="user" ${user.role === 'user' ? 'selected' : ''}>User</option>
              <option value="doctor" ${user.role === 'doctor' ? 'selected' : ''}>Doctor</option>
              <option value="admin" ${user.role === 'admin' ? 'selected' : ''}>Admin</option>
            </select>
          </div>
          
          <div class="form-group">
            <label class="form-label">Status</label>
            <select class="form-select" name="status">
              <option value="active" ${user.status === 'active' ? 'selected' : ''}>Active</option>
              <option value="suspended" ${user.status === 'suspended' ? 'selected' : ''}>Suspended</option>
            </select>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline" onclick="this.closest('.modal-overlay').remove()">Cancel</button>
        <button class="btn btn-primary" onclick="submitEditUserForm()">Save Changes</button>
      </div>
    </div>
  `;
  
  document.body.appendChild(modal);
}

// Function to submit edit form
window.submitEditUserForm = async function () {
  const form = document.getElementById('editUserForm');
  if (!form) return;
  
  const formData = new FormData(form);
  const data = Object.fromEntries(formData.entries());
  
  console.log('Submitting user edit:', data);
  
  try {
    const response = await fetch('api/editUser.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(data),
      credentials: 'same-origin'
    });
    
    // DEBUG: Get response as text first
    const responseText = await response.text();
    console.log('Raw API response:', responseText);
    
    // Try to parse JSON
    let result;
    try {
      result = JSON.parse(responseText);
    } catch (parseError) {
      console.error('Failed to parse JSON. Response was:', responseText);
      MediQA.showNotification('Server returned invalid data. Check console.', 'error');
      return;
    }
    
    if (result.success) {
      MediQA.showNotification("User updated successfully", "success");
      document.querySelector('.modal-overlay')?.remove();
      if (adminControllerInstance?.loadUsersData) {
        setTimeout(() => adminControllerInstance.loadUsersData(), 500);
      }
    } else {
      MediQA.showNotification(result.error || "Failed to update user", "error");
    }
  } catch (error) {
    console.error('Network error:', error);
    MediQA.showNotification("Network error - check console", "error");
  }
};

window.assignQuestion = function (questionId) {
  MediQA.showNotification(
    `Assign question ${questionId} functionality would be implemented here`,
    "info"
  );
};

window.viewQuestion = function (questionId) {
  if (adminControllerInstance) {
    adminControllerInstance.viewQuestion(questionId);
  }
};

// Initialize admin controller when DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
  // Initialize on admin dashboard page rendered via PHP route
  if (document.body && document.body.classList.contains("dashboard-body")) {
    new AdminController();
  }
});



window.activateUser = async function(userId, userName = '') {
  const message = userName 
    ? `Are you sure you want to activate user "${userName}"?`
    : `Are you sure you want to activate user ID: ${userId}?`;
  
  if (!confirm(message)) return;
  
  try {
    MediQA.showNotification("Activating user...", "info");
    
    const response = await fetch('api/activateUser.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ user_id: userId }),
      credentials: 'same-origin'
    });
    
    const data = await response.json();
    
    if (data.success) {
      MediQA.showNotification(`User activated successfully`, "success");
      
      // Refresh user list (this is enough)
      if (adminControllerInstance && adminControllerInstance.loadUsersData) {
        adminControllerInstance.loadUsersData();
      }
      
      // REMOVE THIS LINE:
      // updateUserRowStatus(userId, 'active');
    } else {
      MediQA.showNotification(data.error || "Failed to activate user", "error");
    }
  } catch (error) {
    console.error('Error activating user:', error);
    MediQA.showNotification("Failed to activate user", "error");
  }
};