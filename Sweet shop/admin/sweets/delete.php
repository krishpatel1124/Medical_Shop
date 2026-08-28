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

$id = (int)$_GET['id'];

// Get image name
$result = mysqli_query($conn, "SELECT image FROM sweets WHERE sweet_id='$id'");

if (mysqli_num_rows($result) > 0) {

    $row = mysqli_fetch_assoc($result);

    $imagePath = "../../uploads/sweets/" . $row['image'];

    // Delete image file
    if (!empty($row['image']) && file_exists($imagePath)) {
        unlink($imagePath);
    }

    // Delete database record
    mysqli_query($conn, "DELETE FROM sweets WHERE sweet_id='$id'");
}

// Redirect back to list
header("Location: view.php");
exit();
?>

