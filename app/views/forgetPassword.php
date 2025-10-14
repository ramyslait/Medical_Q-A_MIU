<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Forgot Password - Medical Q&A</title>
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
    }

    .auth-card {
        max-width: 420px;
        width: 100%;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        padding: 2rem;
    }

    .auth-header h1 {
        color: #007bff;
        margin-bottom: 0.5rem;
    }

    .auth-header p {
        color: #555;
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
                        <h1>Forgot Password</h1>
                        <p>Enter your registered email address and we’ll send you a reset link.</p>
                    </div>

                    <form class="auth-form needs-validation" id="forgotPasswordForm" action="/Medical_Q-A_MIU/app/controllers/forgotPasswordController.php" method="POST">
                        <div class="form-group">
                            <label for="email" class="form-label">Email Address</label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form-input"
                                placeholder="Enter your email"
                                required />
                            <div class="form-help">Make sure to enter the email you used for registration.</div>
                        </div>

                        <button type="submit" class="btn btn-primary w-full">
                            <i class="fas fa-paper-plane"></i>
                            Send Reset Link
                        </button>

                        <div class="auth-footer" style="margin-top: 1rem;">
                            <p>
                                Remembered your password?
                                <a href="login.php">Back to Login</a>
                            </p>
                        </div>
                    </form>
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