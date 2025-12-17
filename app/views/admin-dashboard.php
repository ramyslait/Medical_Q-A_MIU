<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard - Medical Q&A</title>
  <link rel="stylesheet" href="css/main.css">
  <link rel="stylesheet" href="css/components.css">
   <!-- Scripts -->
  <script src="js/main.js"></script>
  <script src="js/controllers/adminController.js"></script>

  <?php if (session_status() === PHP_SESSION_NONE) { session_start(); } $user = $_SESSION['user'] ?? null; ?>
  <script>
    // Bridge server session -> client state so client-side auth doesn't misfire
    window.MediQA = window.MediQA || {};
    <?php if ($user): ?>
    MediQA.currentUser = <?php echo json_encode($user); ?>;
    MediQA.isLoggedIn = true;
    MediQA.userRole = <?php echo json_encode($user['role'] ?? null); ?>;
    <?php endif; ?>
  </script>



  <link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    rel="stylesheet" />
  <link rel="icon" href="favicon.svg" type="image/svg+xml" />
</head>

<body class="dashboard-body">

  <!-- Sidebar -->
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
      <div class="sidebar-logo">
        <i class="fas fa-user-md"></i>
        <span>MediQ&A</span>
        <small>Admin Panel</small>
      </div>
      <button class="sidebar-toggle mobile-only" onclick="toggleSidebar()">
        <i class="fas fa-times"></i>
      </button>
    </div>

    <nav class="sidebar-menu">
      <ul class="sidebar-list">
        <li class="sidebar-item">
          <a
            href="#dashboard"
            class="sidebar-link active"
            data-section="dashboard">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a href="#users" class="sidebar-link" data-section="users">
            <i class="fas fa-users"></i>
            <span>Users</span>
            <span id="userCount1" class="badge badge-info">1,234</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a href="#questions" class="sidebar-link" data-section="questions">
            <i class="fas fa-question-circle"></i>
            <span>Questions</span>
            <span id="questionCount" class="badge badge-warning">0</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a href="#answers" class="sidebar-link" data-section="answers">
            <i class="fas fa-comments"></i>
            <span>Answers</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a href="#forum" class="sidebar-link" data-section="forum">
            <i class="fas fa-comments"></i>
            <span>Forum</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a href="#analytics" class="sidebar-link" data-section="analytics">
            <i class="fas fa-chart-bar"></i>
            <span>Analytics</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a href="#settings" class="sidebar-link" data-section="settings">
            <i class="fas fa-cog"></i>
            <span>Settings</span>
          </a>
        </li>
      </ul>
    </nav>

    <div class="sidebar-footer">
      <div class="user-info">
        <img
          src="https://via.placeholder.com/40/2563eb/ffffff?text=<?php echo strtoupper(substr($user['name'] ?? 'A', 0, 2)); ?>"
          alt="User Avatar"
          class="user-avatar" />
        <div class="user-details">
          <div class="user-name"><?php echo htmlspecialchars($user['name'] ?? 'Administrator'); ?></div>
          <div class="user-role"><?php echo ucfirst($user['role'] ?? 'Administrator'); ?></div>
        </div>
      </div>
      <button class="logout-btn" onclick="MediQA.logout()">
        <i class="fas fa-sign-out-alt"></i>
      </button>
    </div>
  </aside>

  <!-- Main Content -->
  <main class="dashboard-main">
    <!-- Top Header -->
    <header class="dashboard-header">
      <div class="header-left">
        <button class="sidebar-toggle desktop-only" onclick="toggleSidebar()">
          <i class="fas fa-bars"></i>
        </button>
        <h1 class="page-title" id="pageTitle">Dashboard</h1>
      </div>
      <div class="header-right">
        <div class="search-box">
          <input type="text" placeholder="Search..." class="search-input" />
          <button class="search-btn">
            <i class="fas fa-search"></i>
          </button>
        </div>
        <div class="header-actions">
          <button class="action-btn" title="Notifications">
            <i class="fas fa-bell"></i>
            <span class="notification-badge">3</span>
          </button>
          <button class="action-btn" title="Messages">
            <i class="fas fa-envelope"></i>
          </button>
        </div>
      </div>
    </header>

    <!-- Dashboard Content -->
    <div class="dashboard-content">
      <!-- Dashboard Overview Section -->
      <section class="dashboard-section active" id="dashboard-section">
        <div class="section-header">
          <h2>Overview</h2>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
              <p id="userCount" class="stat-number"></p>
              <div class="stat-label">Total Users</div>
              <div class="stat-change positive">
                <i class="fas fa-arrow-up"></i>
                +12% this month
              </div>
            </div>
          </div>

          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-question-circle"></i>
            </div>
            <div class="stat-content">
              <div id="pendingQuestionsCount" class="stat-number">0</div>
              <div class="stat-label">Pending Questions</div>
              <div class="stat-change negative">
                <i class="fas fa-arrow-up"></i>
                <span id="pendingQuestionsChange">+0 today</span>
              </div>
            </div>
          </div>

          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-comments"></i>
            </div>
            <div class="stat-content">
              <div id="answersProvidedCount" class="stat-number">0</div>
              <div class="stat-label">Answers Provided</div>
              <div class="stat-change positive">
                <i class="fas fa-arrow-up"></i>
                <span id="answersChange">Loading...</span>
              </div>
            </div>
          </div>

          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-content">
              <div id="accuracyRate" class="stat-number">0%</div>
              <div class="stat-label">Accuracy Rate</div>
              <div class="stat-change positive">
                <i class="fas fa-arrow-up"></i>
                <span id="accuracyChange">Loading...</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Charts and Tables Row -->
        <div class="dashboard-grid">
          <div class="chart-card">
            <div class="card-header">
              <h3>User Activity</h3>
              <div class="card-actions">
                <button class="btn btn-small btn-outline">
                  View Details
                </button>
              </div>
            </div>
            <div class="card-body">
              <div class="chart-placeholder">
                <i class="fas fa-chart-line"></i>
                <p>User Activity Chart</p>
                <small>Chart visualization would go here</small>
              </div>
            </div>
          </div>

          <div class="recent-activity">
            <div class="card-header">
              <h3>Recent Activity</h3>
            </div>
            <div class="card-body">
              <div class="activity-list">
                <div class="activity-item">
                  <div class="activity-icon">
                    <i class="fas fa-user-plus"></i>
                  </div>
                  <div class="activity-content">
                    <div class="activity-text">New user registered</div>
                    <div class="activity-meta">
                      John Smith • 5 minutes ago
                    </div>
                  </div>
                </div>
                <div class="activity-item">
                  <div class="activity-icon">
                    <i class="fas fa-question"></i>
                  </div>
                  <div class="activity-content">
                    <div class="activity-text">New question submitted</div>
                    <div class="activity-meta">
                      "Headache symptoms" • 12 minutes ago
                    </div>
                  </div>
                </div>
                <div class="activity-item">
                  <div class="activity-icon">
                    <i class="fas fa-comment"></i>
                  </div>
                  <div class="activity-content">
                    <div class="activity-text">Answer provided</div>
                    <div class="activity-meta">Dr. Chen • 18 minutes ago</div>
                  </div>
                </div>
                <div class="activity-item">
                  <div class="activity-icon">
                    <i class="fas fa-star"></i>
                  </div>
                  <div class="activity-content">
                    <div class="activity-text">Answer rated helpful</div>
                    <div class="activity-meta">
                      Patient feedback • 25 minutes ago
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Users Management Section -->
      <section class="dashboard-section" id="users-section">
        <div class="section-header">
          <h2>User Management</h2>
          <p>Manage platform users and their permissions</p>
        </div>

        <div class="table-container">
          <table class="table">
            <thead>
              <tr>
                <th>User</th>
                <th>Role</th>
                <th>Status</th>
                <th>Join Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              
            </tbody>
          </table>
        </div>
      </section>

      <!-- Questions Management Section -->
      <section class="dashboard-section" id="questions-section">
        <div class="section-header">
          <h2>Question Management</h2>
          <p>Review and manage submitted questions</p>
        </div>

        <div class="questions-grid">
          <!-- Questions will be loaded dynamically here -->
          <div class="loading-placeholder">
            <div class="spinner"></div>
            <p>Loading questions...</p>
          </div>
        </div>
      </section>

      <!-- Answers Management Section -->
      <section class="dashboard-section" id="answers-section">
        <div class="section-header">
          <h2>Answer Management</h2>
          <p>Review and manage AI-generated answers</p>
        </div>

        <div class="filter-bar" style="margin-bottom: 1.5rem;">
          <select id="answerStatusFilter" class="filter-select" style="max-width: 200px;">
            <option value="">All Answers</option>
            <option value="answered">Answered</option>
            <option value="pending">Pending</option>
            <option value="closed">Closed</option>
          </select>
          <select id="answerCategoryFilter" class="filter-select" style="max-width: 200px;">
            <option value="">All Categories</option>
            <option value="general">General</option>
            <option value="symptoms">Symptoms</option>
            <option value="treatments">Treatments</option>
            <option value="lifestyle">Lifestyle</option>
            <option value="support">Support</option>
          </select>
        </div>

        <div class="answers-grid">
          <!-- Answers will be loaded dynamically here -->
          <div class="loading-placeholder">
            <div class="spinner"></div>
            <p>Loading answers...</p>
          </div>
        </div>
      </section>

      <!-- Forum Management Section -->
      <section class="dashboard-section" id="forum-section">
        <div class="section-header">
          <h2>Forum Management</h2>
          <p>Manage forum discussions and community content</p>
        </div>

        <div class="forum-stats-bar" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
          <div class="stat-card-small">
            <div class="stat-number-small" id="forumTotalQuestions">0</div>
            <div class="stat-label-small">Total Questions</div>
          </div>
          <div class="stat-card-small">
            <div class="stat-number-small" id="forumAnsweredQuestions">0</div>
            <div class="stat-label-small">Answered</div>
          </div>
          <div class="stat-card-small">
            <div class="stat-number-small" id="forumPendingQuestions">0</div>
            <div class="stat-label-small">Pending</div>
          </div>
          <div class="stat-card-small">
            <div class="stat-number-small" id="forumCategories">0</div>
            <div class="stat-label-small">Categories</div>
          </div>
        </div>

        <div class="filter-bar" style="margin-bottom: 1.5rem;">
          <select id="forumStatusFilter" class="filter-select" style="max-width: 200px;">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="answered">Answered</option>
            <option value="closed">Closed</option>
          </select>
          <select id="forumCategoryFilter" class="filter-select" style="max-width: 200px;">
            <option value="">All Categories</option>
            <option value="general">General</option>
            <option value="symptoms">Symptoms</option>
            <option value="treatments">Treatments</option>
            <option value="lifestyle">Lifestyle</option>
            <option value="support">Support</option>
          </select>
          <select id="forumSortFilter" class="filter-select" style="max-width: 200px;">
            <option value="recent">Most Recent</option>
            <option value="oldest">Oldest First</option>
            <option value="category">By Category</option>
          </select>
        </div>

        <div class="forum-management-grid">
          <!-- Forum discussions will be loaded dynamically here -->
          <div class="loading-placeholder">
            <div class="spinner"></div>
            <p>Loading forum discussions...</p>
          </div>
        </div>
      </section>

      <!-- Analytics Section -->
      <section class="dashboard-section" id="analytics-section">
        <div class="section-header">
          <h2>Analytics</h2>
          <p>View detailed analytics and insights</p>
        </div>

        <div class="analytics-grid">
          <!-- User Growth Chart -->
          <div class="analytics-card">
            <div class="analytics-card-header">
              <h3>User Growth (Last 7 Days)</h3>
            </div>
            <div class="analytics-card-body">
              <canvas id="userGrowthChart"></canvas>
            </div>
          </div>

          <!-- Question Volume Chart -->
          <div class="analytics-card">
            <div class="analytics-card-header">
              <h3>Question Volume (Last 7 Days)</h3>
            </div>
            <div class="analytics-card-body">
              <canvas id="questionVolumeChart"></canvas>
            </div>
          </div>

          <!-- Category Distribution -->
          <div class="analytics-card">
            <div class="analytics-card-header">
              <h3>Category Distribution</h3>
            </div>
            <div class="analytics-card-body">
              <div id="categoryDistributionChart" class="chart-container"></div>
            </div>
          </div>

          <!-- Status Distribution -->
          <div class="analytics-card">
            <div class="analytics-card-header">
              <h3>Status Distribution</h3>
            </div>
            <div class="analytics-card-body">
              <div id="statusDistributionChart" class="chart-container"></div>
            </div>
          </div>

          <!-- Monthly Statistics -->
          <div class="analytics-card">
            <div class="analytics-card-header">
              <h3>Monthly Users</h3>
            </div>
            <div class="analytics-card-body">
              <canvas id="monthlyUsersChart"></canvas>
            </div>
          </div>

          <!-- Monthly Questions -->
          <div class="analytics-card">
            <div class="analytics-card-header">
              <h3>Monthly Questions</h3>
            </div>
            <div class="analytics-card-body">
              <canvas id="monthlyQuestionsChart"></canvas>
            </div>
          </div>

          <!-- Key Metrics -->
          <div class="analytics-card full-width">
            <div class="analytics-card-header">
              <h3>Key Metrics</h3>
            </div>
            <div class="analytics-card-body">
              <div class="metrics-grid">
                <div class="metric-item">
                  <div class="metric-label">Average Response Time</div>
                  <div class="metric-value" id="avgResponseTime">-</div>
                </div>
                <div class="metric-item">
                  <div class="metric-label">Top Category</div>
                  <div class="metric-value" id="topCategory">-</div>
                </div>
                <div class="metric-item">
                  <div class="metric-label">Total Categories</div>
                  <div class="metric-value" id="totalCategories">-</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Settings Section -->
      <section class="dashboard-section" id="settings-section">
        <div class="section-header">
          <h2>Settings</h2>
          <p>Manage system settings and configurations</p>
        </div>

        <div class="settings-container">
          <div class="settings-tabs">
            <button class="settings-tab active" data-tab="general">General</button>
            <button class="settings-tab" data-tab="notifications">Notifications</button>
            <button class="settings-tab" data-tab="security">Security</button>
            <button class="settings-tab" data-tab="ai">AI Settings</button>
          </div>

          <!-- General Settings -->
          <div class="settings-content active" id="general-settings">
            <div class="settings-group">
              <h3>Site Information</h3>
              <div class="form-group">
                <label class="form-label">Site Name</label>
                <input type="text" class="form-input" id="siteName" value="Medical Q&A" placeholder="Site Name">
              </div>
              <div class="form-group">
                <label class="form-label">Site Description</label>
                <textarea class="form-textarea" id="siteDescription" rows="3" placeholder="Site Description">Medical Q&A Platform for Community Health Support</textarea>
              </div>
            </div>

            <div class="settings-group">
              <h3>Display Settings</h3>
              <div class="form-group">
                <label class="form-label">Questions Per Page</label>
                <select class="form-select" id="questionsPerPage">
                  <option value="10">10</option>
                  <option value="20" selected>20</option>
                  <option value="50">50</option>
                  <option value="100">100</option>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">Default Sort Order</label>
                <select class="form-select" id="defaultSortOrder">
                  <option value="recent" selected>Most Recent</option>
                  <option value="oldest">Oldest First</option>
                  <option value="category">By Category</option>
                </select>
              </div>
            </div>

            <div class="settings-actions">
              <button class="btn btn-primary" onclick="saveSettings('general')">Save Changes</button>
              <button class="btn btn-outline" onclick="resetSettings('general')">Reset</button>
            </div>
          </div>

          <!-- Notifications Settings -->
          <div class="settings-content" id="notifications-settings">
            <div class="settings-group">
              <h3>Email Notifications</h3>
              <div class="form-group">
                <label class="checkbox-label">
                  <input type="checkbox" id="emailNewQuestion" checked>
                  <span>Notify on new questions</span>
                </label>
              </div>
              <div class="form-group">
                <label class="checkbox-label">
                  <input type="checkbox" id="emailNewUser" checked>
                  <span>Notify on new user registrations</span>
                </label>
              </div>
              <div class="form-group">
                <label class="checkbox-label">
                  <input type="checkbox" id="emailDailyReport">
                  <span>Send daily summary report</span>
                </label>
              </div>
            </div>

            <div class="settings-group">
              <h3>Notification Frequency</h3>
              <div class="form-group">
                <label class="form-label">Report Frequency</label>
                <select class="form-select" id="reportFrequency">
                  <option value="daily">Daily</option>
                  <option value="weekly" selected>Weekly</option>
                  <option value="monthly">Monthly</option>
                </select>
              </div>
            </div>

            <div class="settings-actions">
              <button class="btn btn-primary" onclick="saveSettings('notifications')">Save Changes</button>
              <button class="btn btn-outline" onclick="resetSettings('notifications')">Reset</button>
            </div>
          </div>

          <!-- Security Settings -->
          <div class="settings-content" id="security-settings">
            <div class="settings-group">
              <h3>Password Policy</h3>
              <div class="form-group">
                <label class="form-label">Minimum Password Length</label>
                <input type="number" class="form-input" id="minPasswordLength" value="8" min="6" max="20">
              </div>
              <div class="form-group">
                <label class="checkbox-label">
                  <input type="checkbox" id="requireUppercase">
                  <span>Require uppercase letters</span>
                </label>
              </div>
              <div class="form-group">
                <label class="checkbox-label">
                  <input type="checkbox" id="requireNumbers">
                  <span>Require numbers</span>
                </label>
              </div>
              <div class="form-group">
                <label class="checkbox-label">
                  <input type="checkbox" id="requireSpecialChars">
                  <span>Require special characters</span>
                </label>
              </div>
            </div>

            <div class="settings-group">
              <h3>Session Management</h3>
              <div class="form-group">
                <label class="form-label">Session Timeout (minutes)</label>
                <input type="number" class="form-input" id="sessionTimeout" value="30" min="5" max="1440">
              </div>
            </div>

            <div class="settings-actions">
              <button class="btn btn-primary" onclick="saveSettings('security')">Save Changes</button>
              <button class="btn btn-outline" onclick="resetSettings('security')">Reset</button>
            </div>
          </div>

          <!-- AI Settings -->
          <div class="settings-content" id="ai-settings">
            <div class="settings-group">
              <h3>AI Configuration</h3>
              <div class="form-group">
                <label class="form-label">AI Provider</label>
                <select class="form-select" id="aiProvider">
                  <option value="deepseek" selected>DeepSeek</option>
                  <option value="groq">Groq</option>
                  <option value="openai">OpenAI</option>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">Max Tokens</label>
                <input type="number" class="form-input" id="maxTokens" value="1000" min="100" max="4000">
              </div>
              <div class="form-group">
                <label class="form-label">Temperature</label>
                <input type="number" class="form-input" id="temperature" value="0.7" min="0" max="2" step="0.1">
                <small class="form-help">Lower values make responses more focused, higher values more creative</small>
              </div>
            </div>

            <div class="settings-group">
              <h3>AI Response Settings</h3>
              <div class="form-group">
                <label class="checkbox-label">
                  <input type="checkbox" id="autoGenerateAnswers" checked>
                  <span>Automatically generate answers for new questions</span>
                </label>
              </div>
              <div class="form-group">
                <label class="checkbox-label">
                  <input type="checkbox" id="requireReview">
                  <span>Require admin review before publishing answers</span>
                </label>
              </div>
            </div>

            <div class="settings-actions">
              <button class="btn btn-primary" onclick="saveSettings('ai')">Save Changes</button>
              <button class="btn btn-outline" onclick="resetSettings('ai')">Reset</button>
            </div>
          </div>
        </div>
      </section>
    </div>
  </main>

  <!-- Chart.js Library -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

 
</body>

</html>