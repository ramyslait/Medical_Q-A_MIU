<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect if user is not coming from verified reset code
if (!isset($_SESSION['reset_user_id'])) {
    header('Location: /Medical_Q-A_MIU/public/forgetPassword');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Reset Password - Medical Q&A</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/components.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <link rel="icon" href="favicon.svg" type="image/svg+xml" />
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

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group input {
            width: 100%;
            height: 50px;
            padding: 0 15px;
            font-size: 1rem;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        .alert {
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-weight: 500;
        }

        .alert-error {
            color: #ff4d4f;
            background-color: #ffe6e6;
        }

        .alert-success {
            color: #155724;
            background-color: #d4edda;
        }
    </style>
</head>

<body>
    <?php include '../app/partials/navbar.php'; ?>

    <main class="main-content">
        <div class="container">
            <div class="auth-container">
                <div class="auth-card">
                    <div class="auth-header">
                        <h1>Reset Password</h1>
                        <p>Enter your new password below.</p>
                    </div>

                    <?php if (isset($_SESSION['reset_error'])): ?>
                        <div class="alert alert-error"><?= htmlspecialchars($_SESSION['reset_error']) ?></div>
                        <?php unset($_SESSION['reset_error']); ?>
                    <?php elseif (isset($_SESSION['reset_success'])): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['reset_success']) ?></div>
                        <?php unset($_SESSION['reset_success']); ?>
                    <?php endif; ?>

                    <form class="auth-form" action="/Medical_Q-A_MIU/app/controllers/resetPasswordController.php" method="POST">
                        <div class="form-group">
                            <input type="password" name="password" placeholder="New Password" required minlength="6">
                        </div>
                        <div class="form-group">
                            <input type="password" name="confirmpassword" placeholder="Confirm Password" required minlength="6">
                        </div>

                        <button type="submit" class="btn btn-primary w-full">
                            <i class="fas fa-check"></i> Reset Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <?php include '../app/partials/footer.php'; ?>
</body>

</html>