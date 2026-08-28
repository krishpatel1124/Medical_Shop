<?php
session_start();
include("../../config/db.php");

// Check supplier login
if (!isset($_SESSION['supplier_id'])) {
    header("Location: ../../login.php");
    exit();
}

$supplier_id = $_SESSION['supplier_id'];

// Check sweet ID
if (!isset($_GET['id'])) {
    header("Location: view.php");
    exit();
}

$sweet_id = (int)$_GET['id'];

// Verify sweet belongs to logged-in supplier
$query = mysqli_query($conn,"
SELECT *
FROM sweets
WHERE sweet_id='$sweet_id'
AND supplier_id='$supplier_id'
");

if(mysqli_num_rows($query)==0)
{
    header("Location:view.php");
    exit();
}

$sweet = mysqli_fetch_assoc($query);

/*---------------------------------------------------
Check whether this sweet is used in any order
(Remove this block if you don't have order_item table)
----------------------------------------------------*/

$check = mysqli_query($conn,"
SELECT COUNT(*) AS total
FROM order_item
WHERE sweet_id='$sweet_id'
");

$data = mysqli_fetch_assoc($check);

if($data['total']>0)
{
    echo "
    <script>
    alert('This sweet cannot be deleted because it exists in customer orders.');
    window.location='view.php';
    </script>";
    exit();
}

/* Delete Image */

if($sweet['image']!="")
{
    $path="../../uploads/sweets/".$sweet['image'];

    if(file_exists($path))
    {
        unlink($path);
    }
}

/* Delete Sweet */

$sql="
DELETE FROM sweets
WHERE sweet_id='$sweet_id'
AND supplier_id='$supplier_id'
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
    alert('Unable to delete sweet.');
    window.location='view.php';
    </script>";
}
?>