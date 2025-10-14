<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
$base = dirname($_SERVER['SCRIPT_NAME']);
if ($base === '/' || $base === '\\') $base = '';

// Normalize URI
$current = $_SERVER['REQUEST_URI'];
$current = str_replace($base, '', $current);
$current = rtrim($current, '/');

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - Medical Q&A</title>
  <link rel="stylesheet" href="css/main.css">
  <link rel="stylesheet" href="css/components.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
  <style>
    .error-message {
      color: #ff4d4f;
      background-color: #ffe6e6;
      padding: 10px 15px;
      border-radius: 5px;
      margin-bottom: 15px;
      font-weight: 500;
    }
  </style>
</head>

<body>
  <!-- Navigation Header -->
  <?php include '../app/partials/navbar.php'; ?>

  <!-- Main Content -->
  <main class="main-content">
    <div class="container">
      <div class="auth-container">
        <div class="auth-card style">
          <div class="auth-header">
            <h1>Welcome Back</h1>
            <p>Sign in to your account to continue</p>
          </div>

          <?php
          // Display error message from backend if exists
          if (isset($_SESSION['login_error'])) {
            echo '<div class="error-message">' . $_SESSION['login_error'] . '</div>';
            unset($_SESSION['login_error']); // remove message after displaying
          }
          ?>

          <form class="auth-form needs-validation" id="loginForm" action="/Medical_Q-A_MIU/app/controllers/loginController.php" method="POST">
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
                <a href="<?= $base ?>/forgetPassword" class="forgot-link">Forgot Password?</a>
              </div>
            </div>

            <button type="submit" class="btn btn-primary w-full">
              <i class="fas fa-sign-in-alt"></i>
              Sign In
            </button>

            <div class="auth-divider">
              <span>or</span>
            </div>
          </form>

          <div class="auth-footer">
            <p>
              Don't have an account? <a href="register">Sign up here</a>
            </p>
          </div>
        </div>

        <!-- Info Cards -->
        <div class="auth-info">
          <div class="info-card">
            <div class="info-icon">
              <i class="fas fa-shield-alt"></i>
            </div>
            <h3>Secure & Private</h3>
            <p>Your medical information is protected with enterprise-grade security.</p>
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
  <?php include '../app/partials/footer.php'; ?>

  <!-- Scripts -->
  <script src="js/main.js"></script>

</body>

</html>