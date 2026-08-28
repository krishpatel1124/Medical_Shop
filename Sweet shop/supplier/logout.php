<?php
session_start();

// Remove supplier session variables
unset($_SESSION['supplier_id']);
unset($_SESSION['supplier_name']);

// Destroy all sessions
session_destroy();

// Redirect to common login page
header("Location: ../login.php");
exit();
?>