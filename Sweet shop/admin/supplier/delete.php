<?php
session_start();
include("../../config/db.php");

// Check admin login
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// Check supplier ID
if (!isset($_GET['id'])) {
    header("Location: view.php");
    exit();
}

$supplier_id = (int)$_GET['id'];

// Check if supplier exists
$check = mysqli_query($conn,"
SELECT *
FROM supplier_detail
WHERE supplier_id='$supplier_id'
");

if(mysqli_num_rows($check)==0)
{
    header("Location:view.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| Prevent deletion if supplier is assigned to any sweet
| (Remove this block if your sweets table doesn't have supplier_id)
|--------------------------------------------------------------------------
*/

$used = mysqli_query($conn,"
SELECT COUNT(*) AS total
FROM sweets
WHERE supplier_id='$supplier_id'
");

$data = mysqli_fetch_assoc($used);

if($data['total'] > 0)
{
    echo "
    <script>
    alert('Cannot delete this supplier because it is assigned to one or more sweets.');
    window.location='view.php';
    </script>";
    exit();
}

/* Delete Supplier */

$sql = "
DELETE FROM supplier_detail
WHERE supplier_id='$supplier_id'
";

if(mysqli_query($conn,$sql))
{
    header("Location:view.php?deleted=1");
    exit();
}
else
{
    echo "
    <script>
    alert('Unable to delete supplier.');
    window.location='view.php';
    </script>";
}
?>