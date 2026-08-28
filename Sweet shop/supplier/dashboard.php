<?php
session_start();
include("../config/db.php");

// Check Supplier Login
if (!isset($_SESSION['supplier_id'])) {
    header("Location: ../login.php");
    exit();
}

$supplier_id = $_SESSION['supplier_id'];

// =====================================
// Supplier Information
// =====================================

$supplierQuery = mysqli_query($conn,"
SELECT *
FROM supplier_detail
WHERE supplier_id='$supplier_id'
");

if(!$supplierQuery){
    die("SQL Error: " . mysqli_error($conn));
}

$supplier = mysqli_fetch_assoc($supplierQuery);

if(!$supplier){
    die("Supplier not found.");
}


// =====================================
// Total Sweets
// (using stock table because sweets has no supplier_id)
// =====================================
$sweetQuery = mysqli_query($conn,"
SELECT COUNT(*) AS total_sweets
FROM stock
WHERE supplier_id='$supplier_id'
");

$totalSweets = mysqli_fetch_assoc($sweetQuery)['total_sweets'];

// =====================================
// Total Stock
// =====================================
$stockQuery = mysqli_query($conn,"
SELECT SUM(quantity_in_stock) AS total_stock
FROM stock
WHERE supplier_id='$supplier_id'
");

$stockData = mysqli_fetch_assoc($stockQuery);

$totalStock = $stockData['total_stock'];

if($totalStock=="")
{
    $totalStock=0;
}

// =====================================
// Total Orders
// =====================================
$orderQuery = mysqli_query($conn,"
SELECT COUNT(DISTINCT oi.order_id) AS total_orders
FROM order_items oi
INNER JOIN stock st
ON oi.sweet_id=st.sweet_id
WHERE st.supplier_id='$supplier_id'
");

$orderData = mysqli_fetch_assoc($orderQuery);

$totalOrders = $orderData['total_orders'];

if($totalOrders=="")
{
    $totalOrders=0;
}

// =====================================
// Total Sales
// =====================================
$salesQuery = mysqli_query($conn,"
SELECT SUM(oi.price * oi.quantity) AS total_sales
FROM order_items oi
INNER JOIN stock st
ON oi.sweet_id=st.sweet_id
INNER JOIN `order` o
ON oi.order_id=o.order_id
WHERE st.supplier_id='$supplier_id'
AND LOWER(o.order_status)='delivered'
");

$salesData = mysqli_fetch_assoc($salesQuery);

$totalSales = $salesData['total_sales'];

if($totalSales=="")
{
    $totalSales=0;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Supplier Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>

body{
background:#f5f5f5;
}

.sidebar{
width:240px;
height:100vh;
background:#198754;
position:fixed;
left:0;
top:0;
}

.sidebar h3{
color:#fff;
text-align:center;
padding:20px;
}

.sidebar a{
display:block;
padding:15px 20px;
color:#fff;
text-decoration:none;
}

.sidebar a:hover{
background:#157347;
}

.main{
margin-left:240px;
}

.topbar{
background:#fff;
padding:15px 25px;
box-shadow:0 2px 8px rgba(0,0,0,.1);
}

.content{
padding:30px;
}

.card{
border:none;
box-shadow:0 2px 10px rgba(0,0,0,.1);
}

</style>

</head>

<body>

<div class="sidebar">

<h3>Supplier Panel</h3>

<a href="dashboard.php">
    <i class="bi bi-speedometer2"></i> Dashboard
</a>

<a href="profile.php">
    <i class="bi bi-person"></i> Profile
</a>

<a href="sweets/view.php">
    <i class="bi bi-box"></i> My Sweets
</a>

<a href="stock/view.php">
    <i class="bi bi-archive"></i> Stock
</a>

<a href="orders/view.php">
    <i class="bi bi-bag"></i> Orders
</a>

<a href="change_password.php">
    <i class="bi bi-key"></i> Change Password
</a>

<a href="logout.php">
    <i class="bi bi-box-arrow-right"></i> Logout
</a> 
</div>

<div class="main">

<div class="topbar d-flex justify-content-between">

<h3>Supplier Dashboard</h3>

<div>

Welcome,

<b><?php echo $supplier['supplier_name']; ?></b>

</div>

</div>

<div class="content">

<div class="row">

<div class="col-md-3 mb-4">

<div class="card bg-primary text-white">

<div class="card-body">

<h5>Total Sweets</h5>

<h2><?php echo $totalSweets; ?></h2>

</div>

</div>

</div>

<div class="col-md-3 mb-4">

<div class="card bg-success text-white">

<div class="card-body">

<h5>Total Stock</h5>

<h2><?php echo $totalStock; ?></h2>

</div>

</div>

</div>

<div class="col-md-3 mb-4">

<div class="card bg-warning">

<div class="card-body">

<h5>Total Orders</h5>

<h2><?php echo $totalOrders; ?></h2>

</div>

</div>

</div>

<div class="col-md-3 mb-4">

<div class="card bg-danger text-white">

<div class="card-body">

<h5>Total Sales</h5>

<h2>₹<?php echo number_format($totalSales,2); ?></h2>

</div>

</div>

</div>

</div>
<!-- ================= Recent Orders ================= -->

<div class="card mb-4">

    <div class="card-header bg-success text-white">

        <h5 class="mb-0">Recent Orders</h5>

    </div>

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-bordered table-hover">

                <thead class="table-dark">

                    <tr>

                        <th>Order ID</th>
                        <th>Sweet</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Date</th>

                    </tr>

                </thead>

                <tbody>

<?php

$recent = mysqli_query($conn,"
SELECT
o.order_id,
o.order_date,
o.order_status,
s.sweet_name,
oi.quantity,
oi.price
FROM order_items oi
INNER JOIN `order` o
ON oi.order_id=o.order_id
INNER JOIN stock st
ON oi.sweet_id=st.sweet_id
INNER JOIN sweets s
ON oi.sweet_id=s.sweet_id
WHERE st.supplier_id='$supplier_id'
ORDER BY o.order_date DESC
LIMIT 10
");

if(mysqli_num_rows($recent)>0)
{

while($row=mysqli_fetch_assoc($recent))
{

?>

<tr>

<td><?php echo $row['order_id']; ?></td>

<td><?php echo $row['sweet_name']; ?></td>

<td><?php echo $row['quantity']; ?></td>

<td>₹<?php echo number_format($row['price'],2); ?></td>

<td>

<?php

$status=strtolower($row['order_status']);

if($status=="pending")
{
echo "<span class='badge bg-warning'>Pending</span>";
}
elseif($status=="processing")
{
echo "<span class='badge bg-info'>Processing</span>";
}
elseif($status=="delivered")
{
echo "<span class='badge bg-success'>Delivered</span>";
}
else
{
echo "<span class='badge bg-danger'>Cancelled</span>";
}

?>

</td>

<td><?php echo $row['order_date']; ?></td>

</tr>

<?php

}

}
else
{

echo "<tr><td colspan='6' class='text-center'>No Orders Found</td></tr>";

}

?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<!-- ================= Low Stock ================= -->

<div class="row">

<div class="col-lg-6">

<div class="card">

<div class="card-header bg-danger text-white">

<h5 class="mb-0">Low Stock Alert</h5>

</div>

<div class="card-body">

<?php

$low=mysqli_query($conn,"
SELECT
s.sweet_name,
st.quantity_in_stock,
st.reorder_level
FROM stock st
INNER JOIN sweets s
ON st.sweet_id=s.sweet_id
WHERE st.supplier_id='$supplier_id'
AND st.quantity_in_stock<=st.reorder_level
");

if(mysqli_num_rows($low)>0)
{

?>

<table class="table table-bordered">

<tr>

<th>Sweet</th>

<th>Stock</th>

<th>Reorder Level</th>

</tr>

<?php

while($row=mysqli_fetch_assoc($low))
{

?>

<tr>

<td><?php echo $row['sweet_name']; ?></td>

<td>

<span class="badge bg-danger">

<?php echo $row['quantity_in_stock']; ?>

</span>

</td>

<td><?php echo $row['reorder_level']; ?></td>

</tr>

<?php

}

?>

</table>

<?php

}
else
{

echo "<div class='alert alert-success mb-0'>All products have sufficient stock.</div>";

}

?>

</div>

</div>

</div>

<!-- ================= Stock Summary ================= -->

<div class="col-lg-6">

<div class="card">

<div class="card-header bg-primary text-white">

<h5 class="mb-0">Stock Summary</h5>

</div>

<div class="card-body">

<?php

$list=mysqli_query($conn,"
SELECT
s.sweet_name,
st.quantity_in_stock
FROM stock st
INNER JOIN sweets s
ON st.sweet_id=s.sweet_id
WHERE st.supplier_id='$supplier_id'
ORDER BY s.sweet_name
");

?>

<table class="table table-striped">

<tr>

<th>Sweet</th>

<th>Available Stock</th>

</tr>

<?php

while($row=mysqli_fetch_assoc($list))
{

?>

<tr>

<td><?php echo $row['sweet_name']; ?></td>

<td><?php echo $row['quantity_in_stock']; ?></td>

</tr>

<?php

}

?>

</table>

</div>

</div>

</div>

</div>
<!-- ================= Supplier Information ================= -->

<div class="card mt-4">

    <div class="card-header bg-success text-white">

        <h5 class="mb-0">Supplier Information</h5>

    </div>

    <div class="card-body">

        <table class="table table-bordered">

            <tr>
                <th width="25%">Supplier ID</th>
                <td><?php echo $supplier['supplier_id']; ?></td>
            </tr>

            <tr>
                <th>Supplier Name</th>
                <td><?php echo $supplier['supplier_name']; ?></td>
            </tr>

            <tr>
                <th>Email</th>
                <td><?php echo $supplier['email']; ?></td>
            </tr>

            <tr>
                <th>Mobile</th>
                <td><?php echo $supplier['mobile_no']; ?></td>
            </tr>

            <tr>
                <th>Address</th>
                <td><?php echo $supplier['address']; ?></td>
            </tr>

        </table>

    </div>

</div>

<!-- ================= Footer ================= -->

<footer class="text-center mt-5 mb-3">

    <hr>

    <p class="text-muted">

        © <?php echo date("Y"); ?> Online Sweet Shopping & Storage System

        <br>

        Supplier Dashboard

    </p>

</footer>

</div>
<!-- End Content -->

</div>
<!-- End Main -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>