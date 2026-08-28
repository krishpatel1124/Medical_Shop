<?php
session_start();
include("../../config/db.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: view.php");
    exit();
}

$category_id = (int)$_GET['id'];

/* Check if category exists */

$check = mysqli_query($conn,
"SELECT * FROM category
WHERE category_id='$category_id'");

if(mysqli_num_rows($check)==0)
{
    header("Location:view.php");
    exit();
}

/* Check whether sweets are using this category */

$used = mysqli_query($conn,
"SELECT COUNT(*) AS total
FROM sweets
WHERE category_id='$category_id'");

$data = mysqli_fetch_assoc($used);

if($data['total'] > 0)
{
    echo "
    <script>
        alert('Cannot delete this category because it contains sweets.');
        window.location='view.php';
    </script>";
    exit();
}

/* Delete category */

$sql = "DELETE FROM category
        WHERE category_id='$category_id'";

if(mysqli_query($conn,$sql))
{
    header("Location:view.php?deleted=1");
    exit();
}
else
{
    echo "
    <script>
        alert('Unable to delete category.');
        window.location='view.php';
    </script>";
}
?>