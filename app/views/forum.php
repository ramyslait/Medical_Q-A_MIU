<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Forum - Medical Q&A</title>
  <link rel="stylesheet" href="css/main.css">
  <link rel="stylesheet" href="css/components.css">
  <link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    rel="stylesheet" />
  <link rel="icon" href="favicon.svg" type="image/svg+xml" />
</head>

<body>
  <!-- Navigation Header -->
  <?php include '../app/partials/navbar.php'; ?>

  <!-- Main Content -->
  <main class="main-content">
    <div class="container">
      <div class="forum-header">
        <div class="forum-title">
          <h1>Community Forum</h1>
          <p>
            Connect with others, share experiences, and get support from the
            medical community
          </p>
        </div>
        <div class="forum-actions">
          <button class="btn btn-primary" onclick="startNewDiscussion()">
            <i class="fas fa-plus"></i>
            Start Discussion
          </button>
        </div>
      </div>

      <div class="forum-content">
        <div class="forum-sidebar">
          <div class="filter-card">
            <h3>Filter Discussions</h3>
            <div class="filter-group">
              <label class="filter-label">Category</label>
              <select class="filter-select" id="categoryFilter">
                <option value="">All Categories</option>
                <option value="general">General Discussion</option>
                <option value="symptoms">Symptoms & Conditions</option>
                <option value="treatments">Treatments & Medications</option>
                <option value="lifestyle">Lifestyle & Wellness</option>
                <option value="support">Support & Experiences</option>
              </select>
            </div>
            <div class="filter-group">
              <label class="filter-label">Sort By</label>
              <select class="filter-select" id="sortFilter">
                <option value="recent">Most Recent</option>
                <option value="popular">Most Popular</option>
                <option value="replies">Most Replies</option>
              </select>
            </div>
          </div>

          <div class="stats-card">
            <h3>Forum Statistics</h3>
            <div class="stat-item">
              <span class="stat-number">156</span>
              <span class="stat-label">Active Discussions</span>
            </div>
            <div class="stat-item">
              <span class="stat-number">2,847</span>
              <span class="stat-label">Total Posts</span>
            </div>
            <div class="stat-item">
              <span class="stat-number">1,234</span>
              <span class="stat-label">Community Members</span>
            </div>
          </div>
        </div>

        <div class="forum-main">
          <div class="discussions-list">
            <div class="discussion-item">
              <div class="discussion-avatar">
                <img
                  src="https://via.placeholder.com/50/2563eb/ffffff?text=JS"
                  alt="User Avatar" />
              </div>
              <div class="discussion-content">
                <div class="discussion-header">
                  <h3 class="discussion-title">
                    <a href="discussion-detail.html">Managing chronic pain - what works for you?</a>
                  </h3>
                  <span class="discussion-category badge badge-info">Support</span>
                </div>
                <p class="discussion-preview">
                  I've been dealing with chronic back pain for over a year
                  now. I've tried various treatments but nothing seems to
                  provide long-term relief. What strategies have worked for
                  others in similar situations?
                </p>
                <div class="discussion-meta">
                  <span class="discussion-author">by John Smith</span>
                  <span class="discussion-time">2 hours ago</span>
                  <span class="discussion-replies">
                    <i class="fas fa-comments"></i>
                    12 replies
                  </span>
                  <span class="discussion-views">
                    <i class="fas fa-eye"></i>
                    45 views
                  </span>
                </div>
              </div>
            </div>

            <div class="discussion-item">
              <div class="discussion-avatar">
                <img
                  src="https://via.placeholder.com/50/059669/ffffff?text=MC"
                  alt="User Avatar" />
              </div>
              <div class="discussion-content">
                <div class="discussion-header">
                  <h3 class="discussion-title">
                    <a href="discussion-detail.html">New medication side effects - when to worry?</a>
                  </h3>
                  <span class="discussion-category badge badge-warning">Treatments</span>
                </div>
                <p class="discussion-preview">
                  Started a new prescription last week and I'm experiencing
                  some side effects. How do you know when side effects are
                  normal vs. when you should contact your doctor?
                </p>
                <div class="discussion-meta">
                  <span class="discussion-author">by Maria Chen</span>
                  <span class="discussion-time">4 hours ago</span>
                  <span class="discussion-replies">
                    <i class="fas fa-comments"></i>
                    8 replies
                  </span>
                  <span class="discussion-views">
                    <i class="fas fa-eye"></i>
                    32 views
                  </span>
                </div>
              </div>
            </div>

            <div class="discussion-item">
              <div class="discussion-avatar">
                <img
                  src="https://via.placeholder.com/50/06b6d4/ffffff?text=ER"
                  alt="User Avatar" />
              </div>
              <div class="discussion-content">
                <div class="discussion-header">
                  <h3 class="discussion-title">
                    <a href="discussion-detail.html">Tips for maintaining a healthy lifestyle with
                      diabetes</a>
                  </h3>
                  <span class="discussion-category badge badge-success">Lifestyle</span>
                </div>
                <p class="discussion-preview">
                  Recently diagnosed with Type 2 diabetes and looking for
                  practical tips on managing diet, exercise, and lifestyle
                  changes. What has helped you the most?
                </p>
                <div class="discussion-meta">
                  <span class="discussion-author">by Emily Rodriguez</span>
                  <span class="discussion-time">6 hours ago</span>
                  <span class="discussion-replies">
                    <i class="fas fa-comments"></i>
                    15 replies
                  </span>
                  <span class="discussion-views">
                    <i class="fas fa-eye"></i>
                    67 views
                  </span>
                </div>
              </div>
            </div>

            <div class="discussion-item">
              <div class="discussion-avatar">
                <img
                  src="https://via.placeholder.com/50/dc2626/ffffff?text=AL"
                  alt="User Avatar" />
              </div>
              <div class="discussion-content">
                <div class="discussion-header">
                  <h3 class="discussion-title">
                    <a href="discussion-detail.html">Understanding anxiety symptoms - seeking support</a>
                  </h3>
                  <span class="discussion-category badge badge-danger">Symptoms</span>
                </div>
                <p class="discussion-preview">
                  I've been experiencing anxiety symptoms for the past few
                  months. Feeling overwhelmed and not sure if what I'm
                  experiencing is normal anxiety or something more serious.
                </p>
                <div class="discussion-meta">
                  <span class="discussion-author">by Alex Lee</span>
                  <span class="discussion-time">1 day ago</span>
                  <span class="discussion-replies">
                    <i class="fas fa-comments"></i>
                    23 replies
                  </span>
                  <span class="discussion-views">
                    <i class="fas fa-eye"></i>
                    89 views
                  </span>
                </div>
              </div>
            </div>

            <div class="discussion-item">
              <div class="discussion-avatar">
                <img
                  src="https://via.placeholder.com/50/7c3aed/ffffff?text=SW"
                  alt="User Avatar" />
              </div>
              <div class="discussion-content">
                <div class="discussion-header">
                  <h3 class="discussion-title">
                    <a href="discussion-detail.html">Sleep hygiene tips for better rest</a>
                  </h3>
                  <span class="discussion-category badge badge-info">Lifestyle</span>
                </div>
                <p class="discussion-preview">
                  Struggling with insomnia lately and looking for
                  evidence-based tips to improve sleep quality. What routines
                  or changes have made the biggest difference for you?
                </p>
                <div class="discussion-meta">
                  <span class="discussion-author">by Sarah Wilson</span>
                  <span class="discussion-time">2 days ago</span>
                  <span class="discussion-replies">
                    <i class="fas fa-comments"></i>
                    18 replies
                  </span>
                  <span class="discussion-views">
                    <i class="fas fa-eye"></i>
                    124 views
                  </span>
                </div>
              </div>
            </div>
          </div>

          <div class="pagination">
            <button class="btn btn-outline" disabled>
              <i class="fas fa-chevron-left"></i>
              Previous
            </button>
            <div class="pagination-pages">
              <button class="pagination-page active">1</button>
              <button class="pagination-page">2</button>
              <button class="pagination-page">3</button>
              <span class="pagination-dots">...</span>
              <button class="pagination-page">10</button>
            </div>
            <button class="btn btn-outline">
              Next
              <i class="fas fa-chevron-right"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <?php include '../app/partials/footer.php'; ?>

  <!-- Scripts -->
  <script src="js/main.js"></script>
  <script src="<script src=" js/main.js"></script>/js/controllers/forumController.js"></script>
  <script>
    function startNewDiscussion() {
      if (!MediQA.isLoggedIn) {
        MediQA.showNotification(
          "Please log in to start a discussion",
          "warning"
        );
        setTimeout(() => {
          window.location.href = "login.html";
        }, 1500);
        return;
      }
      // In a real app, this would open a new discussion form or redirect to a new discussion page
      MediQA.showNotification(
        "New discussion feature would be implemented here",
        "info"
      );
    }
  </script>
</body>

</html>