<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - Medical Q&A</title>
  <link rel="stylesheet" href="css/main.css">
  <link rel="stylesheet" href="css/components.css">
  <link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    rel="stylesheet" />
</head>

<body>
  <!-- Navigation Header -->
  <?php include '../app/partials/navbar.php'; ?>

  <!-- Main Content -->
  <main class="main-content">
    <div class="container">
      <div class="auth-container">
        <div class="auth-card">
          <div class="auth-header">
            <h1>Welcome Back</h1>
            <p>Sign in to your account to continue</p>
          </div>

          <form class="auth-form needs-validation" id="loginForm">
            <div class="form-group">
              <label for="email" class="form-label">Email Address</label>
              <input
                type="email"
                id="email"
                name="email"
                class="form-input"
                required />
            </div>

            <div class="form-group">
              <label for="password" class="form-label">Password</label>
              <input
                type="password"
                id="password"
                name="password"
                class="form-input"
                required />
            </div>

            <div class="form-group">
              <div class="form-options">
                <label class="checkbox-label">
                  <input type="checkbox" name="remember" />
                  <span class="checkmark"></span>
                  Remember me
                </label>
                <a href="forgot-password.html" class="forgot-link">Forgot Password?</a>
              </div>
            </div>

            <button type="submit" class="btn btn-primary w-full">
              <i class="fas fa-sign-in-alt"></i>
              Sign In
            </button>

            <div class="auth-divider">
              <span>or</span>
            </div>

            <button
              type="button"
              class="btn btn-outline w-full"
              onclick="demoLogin()">
              <i class="fas fa-user-md"></i>
              Demo Login (Admin)
            </button>
          </form>

          <div class="auth-footer">
            <p>
              Don't have an account? <a href="register.html">Sign up here</a>
            </p>
          </div>
        </div>

        <div class="auth-info">
          <div class="info-card">
            <div class="info-icon">
              <i class="fas fa-shield-alt"></i>
            </div>
            <h3>Secure & Private</h3>
            <p>
              Your medical information is protected with enterprise-grade
              security.
            </p>
          </div>

          <div class="info-card">
            <div class="info-icon">
              <i class="fas fa-user-md"></i>
            </div>
            <h3>Verified Experts</h3>
            <p>Get answers from certified healthcare professionals.</p>
          </div>

          <div class="info-card">
            <div class="info-icon">
              <i class="fas fa-clock"></i>
            </div>
            <h3>Quick Response</h3>
            <p>Receive answers within minutes, not hours.</p>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer class="footer">
    <div class="container">
      <div class="footer-content">
        <div class="footer-section">
          <div class="footer-logo">
            <i class="fas fa-user-md"></i>
            <span>MediQ&A</span>
          </div>
          <p>
            Your trusted source for accurate medical information and
            professional healthcare guidance.
          </p>
        </div>
        <div class="footer-section">
          <h3>Quick Links</h3>
          <ul>
            <li><a href="..//home">Home</a></li>
            <li><a href="ask-question.html">Ask Question</a></li>
            <li><a href="forum.html">Forum</a></li>
            <li><a href="feedback.html">Contact</a></li>
          </ul>
        </div>
        <div class="footer-section">
          <h3>Contact Info</h3>
          <ul>
            <li><i class="fas fa-envelope"></i> info@mediqa.com</li>
            <li><i class="fas fa-phone"></i> +1 (555) 123-4567</li>
            <li>
              <i class="fas fa-map-marker-alt"></i> Medical District, Health
              City
            </li>
          </ul>
        </div>
      </div>
      <div class="footer-bottom">
        <p>&copy; 2024 MediQ&A. All rights reserved.</p>
      </div>
    </div>
  </footer>

  <!-- Scripts -->
  <script src="js/main.js"></script>
  <script src="<script src=" js/main.js"></script>/js/controllers/authController.js"></script>
  <script>
    // Demo login function
    function demoLogin() {
      const demoUser = {
        id: "demo-admin-001",
        name: "Dr. Sarah Johnson",
        email: "admin@mediqa.com",
        role: "admin",
        avatar: "https://via.placeholder.com/100",
        joinDate: "2024-01-15",
      };

      MediQA.saveUserToStorage(demoUser);
      MediQA.showNotification(
        "Demo login successful! Welcome, Dr. Johnson.",
        "success"
      );
      setTimeout(() => {
        window.location.href = "admin-dashboard.html";
      }, 1500);
    }
  </script>
</body>

</html>