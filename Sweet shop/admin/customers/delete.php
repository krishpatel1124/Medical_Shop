<?php
session_start();
include("../../config/db.php");

// Check admin login
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// Check customer ID
if (!isset($_GET['id'])) {
    header("Location: view.php");
    exit();
}

$customer_id = (int)$_GET['id'];

// Check if customer exists
$check = mysqli_query($conn,
"SELECT * FROM customer_detail
 WHERE customer_id='$customer_id'");

if(mysqli_num_rows($check)==0)
{
    header("Location: view.php");
    exit();
}

// Delete customer
$sql = "DELETE FROM customer_detail
        WHERE customer_id='$customer_id'";

if(mysqli_query($conn,$sql))
{
    header("Location:view.php?deleted=1");
    exit();
}
else
{
    echo "
    <script>
    alert('Unable to delete customer.');
    window.location='view.php';
    </script>";
}
?>