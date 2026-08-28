<?php
session_start();
include("../../config/db.php");

// Check admin login
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// Check payment ID
if (!isset($_GET['id'])) {
    header("Location: view.php");
    exit();
}

$payment_id = (int)$_GET['id'];

// Check if payment exists
$check = mysqli_query($conn,
"SELECT * FROM payment
 WHERE payment_id='$payment_id'");

if(mysqli_num_rows($check)==0)
{
    header("Location:view.php");
    exit();
}

$row = mysqli_fetch_assoc($check);

$order_id = $row['order_id'];

// Delete payment
if(mysqli_query($conn,
"DELETE FROM payment
 WHERE payment_id='$payment_id'"))
{

    // Optional: Reset order status after payment deletion
    mysqli_query($conn,
    "UPDATE `order`
     SET order_status='Pending'
     WHERE order_id='$order_id'");

    header("Location:view.php?deleted=1");
    exit();
}
else
{
    echo "
    <script>
        alert('Unable to delete payment.');
        window.location='view.php';
    </script>";
}
?>