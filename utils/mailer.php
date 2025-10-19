<?php
// Include PHPMailer manually (based on your project structure)
require_once __DIR__ . '/../PHPMailer-master/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer-master/src/SMTP.php';
require_once __DIR__ . '/../PHPMailer-master/src/Exception.php';
require_once '../../vendor/autoload.php';

use Dotenv\Dotenv;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$EMAIL = $_ENV['MAIL_USERNAME'];
$PASSWORD = $_ENV['MAIL_PASSWORD'];

function sendVerificationEmail($recipientEmail, $recipientName, $verificationCode)
{
    global $EMAIL, $PASSWORD;

    $mail = new PHPMailer(true);

    try {
        // SMTP server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $EMAIL;
        $mail->Password   = $PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Sender & recipient
        $mail->setFrom($EMAIL, 'MediQ&A');
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
    global $EMAIL, $PASSWORD;

    $mail = new PHPMailer(true);

    try {
        // SMTP settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $EMAIL;
        $mail->Password   = $PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Sender & recipient
        $mail->setFrom($EMAIL, 'MediQ&A');
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
