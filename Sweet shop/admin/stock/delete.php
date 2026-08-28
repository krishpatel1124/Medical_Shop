<?php
session_start();
include("../../config/db.php");

// Check admin login
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// Check stock ID
if (!isset($_GET['id'])) {
    header("Location: view.php");
    exit();
}

$stock_id = (int)$_GET['id'];

// Check if stock record exists
$check = mysqli_query($conn,
"SELECT * FROM stock
WHERE stock_id='$stock_id'");

if(mysqli_num_rows($check)==0)
{
    header("Location:view.php");
    exit();
}

// Delete stock record
$sql = "DELETE FROM stock
        WHERE stock_id='$stock_id'";

if(mysqli_query($conn,$sql))
{
    header("Location:view.php?deleted=1");
    exit();
}
else
{
    echo "
    <script>
        alert('Unable to delete stock record.');
        window.location='view.php';
    </script>";
}
?>