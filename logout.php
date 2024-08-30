<?php
session_start();

// Clear all session variables
session_unset();

// Destroy the session
session_destroy();

// Redirect to the landing page (index.html)
header("Location: index.html");
exit;
?>

