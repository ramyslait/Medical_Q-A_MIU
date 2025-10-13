<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register - Medical Q&A</title>
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
            <h1>Join MediQ&A</h1>
            <p>Create your account to get started</p>
          </div>

          <!-- ✅ Display session messages -->
          <?php if (isset($_SESSION['register_error'])): ?>
            <div class="alert alert-error">
              <?= $_SESSION['register_error']; ?>
            </div>
            <?php unset($_SESSION['register_error']); ?>
          <?php endif; ?>

          <?php if (isset($_SESSION['register_success'])): ?>
            <div class="alert alert-success">
              <?= $_SESSION['register_success']; ?>
            </div>
            <?php unset($_SESSION['register_success']); ?>
          <?php endif; ?>

          <form class="auth-form needs-validation" id="registerForm"
            action="/Medical_Q-A_MIU/app/controllers/registerController.php"
            method="POST">

            <div class="form-group">
              <label for="role" class="form-label">I am a</label>
              <select id="role" name="role" class="form-select">
                <option value="">Select your role</option>
                <option value="patient" <?= (($_SESSION['form_data']['role'] ?? '') === 'patient') ? 'selected' : '' ?>>Patient</option>
                <option value="provider" <?= (($_SESSION['form_data']['role'] ?? '') === 'provider') ? 'selected' : '' ?>>Healthcare Provider</option>
              </select>
            </div>

            <div class="form-group">
              <label for="fullName" class="form-label">Full Name</label>
              <input
                type="text"
                id="fullName"
                name="fullName"
                class="form-input"
                value="<?= htmlspecialchars($_SESSION['form_data']['fullName'] ?? '') ?>" />
            </div>

            <div class="form-group">
              <label for="email" class="form-label">Email Address</label>
              <input
                type="email"
                id="email"
                name="email"
                class="form-input"
                value="<?= htmlspecialchars($_SESSION['form_data']['email'] ?? '') ?>" />
            </div>

            <div class="form-group">
              <label for="password" class="form-label">Password</label>
              <input
                type="password"
                id="password"
                name="password"
                class="form-input"

                minlength="6" />
              <div class="form-help">Must be at least 6 characters long</div>
            </div>

            <div class="form-group">
              <label for="confirmPassword" class="form-label">Confirm Password</label>
              <input
                type="password"
                id="confirmPassword"
                name="confirmPassword"
                class="form-input" />
            </div>

            <div class="form-group">
              <label class="checkbox-label">
                <input type="checkbox" name="terms" />
                <span class="checkmark"></span>
                I agree to the
                <a href="terms.html" target="_blank">Terms of Service</a> and
                <a href="privacy.html" target="_blank">Privacy Policy</a>
              </label>
            </div>

            <div class="form-group">
              <label class="checkbox-label">
                <input type="checkbox" name="newsletter" />
                <span class="checkmark"></span>
                Subscribe to our newsletter for health tips and updates
              </label>
            </div>

            <button type="submit" class="btn btn-primary w-full">
              <i class="fas fa-user-plus"></i>
              Create Account
            </button>

            <div class="auth-divider">
              <span>or</span>
            </div>
          </form>

          <div class="auth-footer">
            <p>
              Already have an account? <a href="login.php">Sign in here</a>
            </p>
          </div>

          <?php unset($_SESSION['form_data']); ?>
        </div>

        <div class="auth-info">
          <div class="info-card">
            <div class="info-icon">
              <i class="fas fa-heart"></i>
            </div>
            <h3>Patient Benefits</h3>
            <ul>
              <li>Ask medical questions anonymously</li>
              <li>Get expert answers within hours</li>
              <li>Access health resources</li>
              <li>Join supportive community</li>
            </ul>
          </div>

          <div class="info-card">
            <div class="info-icon">
              <i class="fas fa-user-md"></i>
            </div>
            <h3>Provider Benefits</h3>
            <ul>
              <li>Help patients with medical guidance</li>
              <li>Build professional reputation</li>
              <li>Access to medical resources</li>
              <li>Continuing education credits</li>
            </ul>
          </div>

          <div class="info-card">
            <div class="info-icon">
              <i class="fas fa-shield-alt"></i>
            </div>
            <h3>Privacy & Security</h3>
            <ul>
              <li>HIPAA compliant platform</li>
              <li>Encrypted data transmission</li>
              <li>Secure user authentication</li>
              <li>Privacy-first approach</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <?php include '../app/partials/footer.php'; ?>

  <!-- Scripts -->
  <script src="js/main.js"></script>
  <script src="js/controllers/authController.js"></script>
</body>

</html>