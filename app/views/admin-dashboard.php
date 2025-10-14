<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard - Medical Q&A</title>
  <link rel="stylesheet" href="css/main.css">
  <link rel="stylesheet" href="css/components.css">
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
            <span class="badge badge-info">1,234</span>
          </a>
        </li>
        <li class="sidebar-item">
          <a href="#questions" class="sidebar-link" data-section="questions">
            <i class="fas fa-question-circle"></i>
            <span>Questions</span>
            <span class="badge badge-warning">45</span>
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
          src="https://via.placeholder.com/40/2563eb/ffffff?text=SA"
          alt="User Avatar"
          class="user-avatar" />
        <div class="user-details">
          <div class="user-name">Dr. Sarah Johnson</div>
          <div class="user-role">Administrator</div>
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
          <p>
            Welcome back, Dr. Johnson. Here's what's happening with your
            platform today.
          </p>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
              <div class="stat-number">1,234</div>
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
              <div class="stat-number">45</div>
              <div class="stat-label">Pending Questions</div>
              <div class="stat-change negative">
                <i class="fas fa-arrow-up"></i>
                +5 today
              </div>
            </div>
          </div>

          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-comments"></i>
            </div>
            <div class="stat-content">
              <div class="stat-number">2,156</div>
              <div class="stat-label">Answers Provided</div>
              <div class="stat-change positive">
                <i class="fas fa-arrow-up"></i>
                +8% this week
              </div>
            </div>
          </div>

          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-content">
              <div class="stat-number">95.2%</div>
              <div class="stat-label">Accuracy Rate</div>
              <div class="stat-change positive">
                <i class="fas fa-arrow-up"></i>
                +2.1% improvement
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
              <tr>
                <td>
                  <div class="user-cell">
                    <img
                      src="https://via.placeholder.com/40/2563eb/ffffff?text=SA"
                      alt="Avatar"
                      class="user-avatar-small" />
                    <div>
                      <div class="user-name">Dr. Sarah Johnson</div>
                      <div class="user-email">sarah.johnson@hospital.com</div>
                    </div>
                  </div>
                </td>
                <td><span class="badge badge-info">Admin</span></td>
                <td><span class="badge badge-success">Active</span></td>
                <td>Jan 15, 2024</td>
                <td>
                  <button class="btn btn-small btn-outline">Edit</button>
                  <button class="btn btn-small btn-danger">Suspend</button>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="user-cell">
                    <img
                      src="https://via.placeholder.com/40/059669/ffffff?text=MC"
                      alt="Avatar"
                      class="user-avatar-small" />
                    <div>
                      <div class="user-name">Dr. Michael Chen</div>
                      <div class="user-email">michael.chen@clinic.com</div>
                    </div>
                  </div>
                </td>
                <td><span class="badge badge-warning">Provider</span></td>
                <td><span class="badge badge-success">Active</span></td>
                <td>Jan 20, 2024</td>
                <td>
                  <button class="btn btn-small btn-outline">Edit</button>
                  <button class="btn btn-small btn-danger">Suspend</button>
                </td>
              </tr>
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
          <div class="question-card">
            <div class="question-header">
              <h4>Persistent headache for 3 days</h4>
              <span class="badge badge-warning">Pending</span>
            </div>
            <div class="question-meta">
              <span class="question-category">Symptoms</span>
              <span class="question-date">2 hours ago</span>
            </div>
            <p class="question-preview">
              I've been experiencing a constant headache for the past 3 days.
              It's mostly on the left side of my head and gets worse when I
              move around...
            </p>
            <div class="question-actions">
              <button class="btn btn-small btn-primary">
                Assign to Provider
              </button>
              <button class="btn btn-small btn-outline">View Details</button>
            </div>
          </div>

          <div class="question-card">
            <div class="question-header">
              <h4>Medication side effects</h4>
              <span class="badge badge-success">Answered</span>
            </div>
            <div class="question-meta">
              <span class="question-category">Treatments</span>
              <span class="question-date">1 day ago</span>
            </div>
            <p class="question-preview">
              I started taking a new medication last week and I'm experiencing
              some side effects. Should I be concerned?
            </p>
            <div class="question-actions">
              <button class="btn btn-small btn-outline">View Answer</button>
              <button class="btn btn-small btn-success">Mark Resolved</button>
            </div>
          </div>
        </div>
      </section>

      <!-- Other sections would be similar... -->
    </div>
  </main>

  <!-- Scripts -->
  <script src="js/main.js"></script>
  <script src="js/controllers/adminController.js"></script>
</body>

</html>