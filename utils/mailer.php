<?php
// Include PHPMailer manually (based on your project structure)
require_once __DIR__ . '/../PHPMailer-master/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer-master/src/SMTP.php';
require_once __DIR__ . '/../PHPMailer-master/src/Exception.php';

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



// In your mailer.php file, add this function:
function sendDoctorApprovalEmail($user_email, $user_name, $question_title, $answer, $doctor_name) {
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
        $mail->addAddress($user_email, $user_name);
        
        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Your Question Has Been Answered - MediQ&A';
        
        $formatted_answer = nl2br(htmlspecialchars($answer));
        
        $mail->Body = "
            <!DOCTYPE html>
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: #007bff; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
                    .content { background: #f9f9f9; padding: 20px; border-radius: 0 0 5px 5px; }
                    .answer-box { background: white; border-left: 4px solid #007bff; padding: 15px; margin: 20px 0; }
                    .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>MediQ&A - Your Question Has Been Answered</h2>
                    </div>
                    
                    <div class='content'>
                        <p>Dear <strong>$user_name</strong>,</p>
                        
                        <p>We're pleased to inform you that your question has been reviewed and answered by our medical team.</p>
                        
                        <h3>Your Question:</h3>
                        <p><strong>$question_title</strong></p>
                        
                        <h3>Medical Response:</h3>
                        <div class='answer-box'>
                            $formatted_answer
                        </div>
                        
                        <p><em>This answer has been reviewed and approved by: <strong>$doctor_name</strong></em></p>
                        
                        <p><strong>Important Disclaimer:</strong><br>
                        This information is for educational purposes only and is not a substitute for professional medical advice. 
                        Always consult with a qualified healthcare provider for personal medical concerns.</p>
                        
                        <p>You can view this answer anytime in your MediQ&A account.</p>
                        
                        <p>Thank you for using MediQ&A!</p>
                        
                        <div class='footer'>
                            <p>MediQ&A Medical Question & Answer System<br>
                            This is an automated message, please do not reply to this email.</p>
                        </div>
                    </div>
                </div>
            </body>
            </html>
        ";
        
        $mail->AltBody = "Dear $user_name,\n\n" .
            "Your question '$question_title' has been answered by our medical team.\n\n" .
            "Answer: $answer\n\n" .
            "This answer has been reviewed and approved by: $doctor_name\n\n" .
            "Important: This is for educational purposes only. Always consult with a healthcare provider for personal medical concerns.\n\n" .
            "Thank you for using MediQ&A!";
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("Doctor approval email error for $user_email: {$mail->ErrorInfo}");
        return false;
    }
}


// Add this function to your existing mailer.php
function sendDoctorDisapprovalEmail($user_email, $user_name, $question_body, $doctor_answer, $doctor_name) {
    global $EMAIL, $PASSWORD;
    
    $mail = new PHPMailer(true);
    
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $EMAIL;
        $mail->Password = $PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        $mail->setFrom($EMAIL, 'MediQ&A');
        $mail->addAddress($user_email, $user_name);
        
        $mail->isHTML(true);
        $mail->Subject = 'Update on Your Medical Question';
        
        $mail->Body = "
            <h3>Your Question Has Been Reviewed</h3>
            <p>Dear $user_name,</p>
            <p>Your question has been reviewed by Dr. $doctor_name.</p>
            <p><strong>Your Question:</strong><br>" . nl2br(htmlspecialchars($question_body)) . "</p>
            <p><strong>Doctor's Answer:</strong><br>" . nl2br(htmlspecialchars($doctor_answer)) . "</p>
            <p><em>The AI answer was not approved for medical accuracy. This doctor-reviewed answer has been provided instead.</em></p>
        ";
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("Disapproval email error: " . $e->getMessage());
        return false;
    }
}