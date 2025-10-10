<?php
// Include PHPMailer manually (based on your project structure)
require_once __DIR__ . '/../PHPMailer-master/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer-master/src/SMTP.php';
require_once __DIR__ . '/../PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendVerificationEmail($recipientEmail, $recipientName, $verificationCode)
{
    $mail = new PHPMailer(true);

    try {
        // SMTP server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'nabilramy2005@gmail.com'; // ✅ Replace with your Gmail
        $mail->Password   = 'sfqr flpk flsk vpxm'; // ✅ Use an app password, not your real one
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Sender & recipient
        $mail->setFrom('your_email@gmail.com', 'MediQ&A');
        $mail->addAddress($recipientEmail, $recipientName);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Verify Your MediQ&A Account';
        $mail->Body = "
            <h2>Email Verification</h2>
            <p>Hi <strong>$recipientName</strong>,</p>
            <p>Thank you for registering at MediQ&A.</p>
            <p>Your verification code is:</p>
            <h3 style='color:#007bff;'>$verificationCode</h3>
            <p>This code will expire in 1 hour.</p>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}
function sendResetPasswordCode($recipientEmail, $recipientName, $resetCode)
{
    $mail = new PHPMailer(true);

    try {
        // SMTP settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'nabilramy2005@gmail.com'; // ✅ Replace with your Gmail
        $mail->Password   = 'sfqr flpk flsk vpxm';     // ✅ Use app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Sender & recipient
        $mail->setFrom('nabilramy2005@gmail.com', 'MediQ&A');
        $mail->addAddress($recipientEmail, $recipientName);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Your MediQ&A Password Reset Code';
        $mail->Body = "
            <h2>Password Reset Code</h2>
            <p>Hi <strong>$recipientName</strong>,</p>
            <p>Your 6-character reset code is:</p>
            <h3 style='color:#007bff;'>$resetCode</h3>
            <p>This code will expire in 1 hour.</p>
            <p>If you didn't request a password reset, ignore this email.</p>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

/**
 * Generate a secure 6-character alphanumeric code
 *
 * @return string
 */
function generateResetCode()
{
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomCode = '';
    for ($i = 0; $i < 6; $i++) {
        $randomCode .= $characters[random_int(0, $charactersLength - 1)];
    }
    return $randomCode;
}
