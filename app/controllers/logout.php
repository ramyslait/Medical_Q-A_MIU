<?php
// Start the session if you're using session too
session_start();

// Unset all session variables (optional if you use sessions)
session_unset();
session_destroy();

// Delete the 'user' cookie by setting its expiration in the past
setcookie('user', '', time() - 3600, "/"); // "/" ensures it works across your domain


// Redirect to home or login page
header("Location: /Medical_Q-A_MIU/public/home");
exit();
?>
