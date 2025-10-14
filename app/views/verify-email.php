<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Verify Email - Medical Q&A</title>
  <link rel="stylesheet" href="css/main.css">
  <link rel="stylesheet" href="css/components.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
  <link rel="icon" href="favicon.svg" type="image/svg+xml" />
</head>
<style>
  .auth-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 80vh;
    /* or 100vh for full viewport */
  }
</style>

<body>
  <!-- Navigation Header -->
  <?php include '../app/partials/navbar.php'; ?>

  <!-- Main Content -->
  <main class="main-content">
    <div class="container">
      <div class="auth-container">
        <div class="auth-card">
          <div class="auth-header">
            <h1>Verify Your Email</h1>
            <p>Enter the 6-digit code sent to your email to complete registration.</p>
          </div>

          <form class="auth-form needs-validation" id="verifyForm" action="/Medical_Q-A_MIU/app/controllers/verifyEmailController.php" method="POST">
            <div class="form-group">
              <label for="verification_code" class="form-label">Verification Code</label>
              <input
                type="text"
                id="verification_code"
                name="verification_code"
                class="form-input"
                placeholder="Enter 6-digit code"
                required
                pattern="\d{6}"
                maxlength="6" />
              <div class="form-help">Please enter the 6-digit code sent to your email.</div>
            </div>

            <button type="submit" class="btn btn-primary w-full">
              <i class="fas fa-check"></i>
              Verify Email
            </button>

            <div class="auth-divider">
              <span>or</span>
            </div>

            <button type="button" class="btn btn-outline w-full" onclick="resendCode()">
              <i class="fas fa-envelope"></i>
              Resend Code
            </button>
          </form>

          <div class="auth-footer">
            <p>
              Already verified? <a href="login.html">Sign in here</a>
            </p>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <?php include '../app/partials/footer.php'; ?>
  <!-- Scripts -->
  <script src="js/main.js"></script>
  <script>
    function resendCode() {
      alert("A new verification code has been sent to your email.");
      // You can call an endpoint to resend the code using AJAX if needed
    }
  </script>
</body>

</html>