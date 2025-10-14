<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['reset_request_sent'])) {
    header('Location: /Medical_Q-A_MIU/public/forgetPassword');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Enter Reset Code - Medical Q&A</title>
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

        .code-inputs {
            display: flex;
            justify-content: space-between;
            margin: 1rem 0;
        }

        .code-inputs input {
            width: 50px;
            height: 50px;
            text-align: center;
            font-size: 1.5rem;
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
                        <h1>Enter Reset Code</h1>
                        <p>Please enter the 6-character code sent to your email.</p>
                    </div>

                    <?php if (isset($_SESSION['reset_error'])): ?>
                        <div class="alert alert-error"><?= htmlspecialchars($_SESSION['reset_error']) ?></div>
                        <?php unset($_SESSION['reset_error']); ?>
                    <?php elseif (isset($_SESSION['reset_success'])): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['reset_success']) ?></div>
                        <?php unset($_SESSION['reset_success']); ?>
                    <?php endif; ?>

                    <form class="auth-form" id="submitResetCodeForm" action="/Medical_Q-A_MIU/app/controllers/submitResetCodeController.php" method="POST">
                        <div class="form-group code-inputs">
                            <input type="text" maxlength="1" pattern="[A-Za-z0-9]" required>
                            <input type="text" maxlength="1" pattern="[A-Za-z0-9]" required>
                            <input type="text" maxlength="1" pattern="[A-Za-z0-9]" required>
                            <input type="text" maxlength="1" pattern="[A-Za-z0-9]" required>
                            <input type="text" maxlength="1" pattern="[A-Za-z0-9]" required>
                            <input type="text" maxlength="1" pattern="[A-Za-z0-9]" required>
                        </div>

                        <!-- Hidden input to store combined code -->
                        <input type="hidden" name="reset_code" id="reset_code">

                        <button type="submit" class="btn btn-primary w-full">
                            <i class="fas fa-check"></i> Submit Code
                        </button>

                        <div class="auth-footer" style="margin-top: 1rem;">
                            <p>Didn't receive a code? <a href="<?= $base ?>/public/forgetPassword">Resend Code</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <?php include '../app/partials/footer.php'; ?>

    <script src="js/main.js"></script>
    <script>
        const inputs = document.querySelectorAll('.code-inputs input');
        const hiddenInput = document.getElementById('reset_code');

        // Handle input and auto-uppercase
        inputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                // Uppercase the current input
                input.value = input.value.toUpperCase();

                // Move to next input if a single character entered
                if (input.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }

                // Update hidden input
                hiddenInput.value = Array.from(inputs).map(i => i.value).join('');
            });

            // Handle backspace to move focus
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !input.value && index > 0) {
                    inputs[index - 1].focus();
                }
            });

            // Handle paste event
            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const pasteData = e.clipboardData.getData('text').toUpperCase().replace(/[^A-Z0-9]/g, '');
                pasteData.split('').forEach((char, i) => {
                    if (index + i < inputs.length) {
                        inputs[index + i].value = char;
                    }
                });
                // Update hidden input
                hiddenInput.value = Array.from(inputs).map(i => i.value).join('');
            });
        });

        // On form submit, combine all inputs
        document.getElementById('submitResetCodeForm').addEventListener('submit', function() {
            hiddenInput.value = Array.from(inputs).map(i => i.value).join('');
        });
    </script>
</body>

</html>