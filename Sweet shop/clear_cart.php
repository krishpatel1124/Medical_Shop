<?php
session_start();
include("config/db.php");

// Check login
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];

// Delete all items from customer's cart
$sql = "DELETE FROM cart WHERE customer_id='$customer_id'";

if (mysqli_query($conn, $sql)) {
    header("Location: cart.php?msg=cleared");
    exit();
} else {
    echo "Error: " . mysqli_error($conn);
}
?>