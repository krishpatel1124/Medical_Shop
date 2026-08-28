<?php
session_start();
include("../../config/db.php");

// Check admin login
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// Total Orders
$totalOrders = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM orders")
);

// Total Sales
$totalSales = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT IFNULL(SUM(total_amount),0) AS total FROM orders")
);

// Total Customers
$totalCustomers = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM users")
);

// Total Sweets
$totalSweets = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM sweets")
);
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Sales Report</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    background:#f8f9fa;
}

.card{
    border:none;
    border-radius:12px;
}

.report-card{
    color:white;
}

</style>

</head>

<body>

<div class="container py-5">

<div class="d-flex justify-content-between align-items-center mb-4">

<h2>Sales Report Dashboard</h2>

<a href="../dashboard.php" class="btn btn-secondary">
Back to Dashboard
</a>

</div>

<div class="row g-4">

<div class="col-md-3">

<div class="card bg-primary report-card shadow">

<div class="card-body text-center">

<h5>Total Orders</h5>

<h2>

<?php echo $totalOrders['total']; ?>

</h2>

</div>

</div>

</div>

<div class="col-md-3">

<div class="card bg-success report-card shadow">

<div class="card-body text-center">

<h5>Total Sales</h5>

<h2>

₹<?php echo number_format($totalSales['total'],2); ?>

</h2>

</div>

</div>

</div>

<div class="col-md-3">

<div class="card bg-info report-card shadow">

<div class="card-body text-center">

<h5>Total Customers</h5>

<h2>

<?php echo $totalCustomers['total']; ?>

</h2>

</div>

</div>

</div>

<div class="col-md-3">

<div class="card bg-warning report-card shadow">

<div class="card-body text-center">

<h5>Total Sweets</h5>

<h2>

<?php echo $totalSweets['total']; ?>

</h2>

</div>

</div>

</div>

</div>

<hr class="my-5">

<?php

// Monthly Sales
$monthlySales = mysqli_query($conn,
"SELECT DATE_FORMAT(order_date,'%Y-%m') AS month,
        COUNT(order_id) AS total_orders,
        SUM(total_amount) AS total_sales
 FROM orders
 GROUP BY DATE_FORMAT(order_date,'%Y-%m')
 ORDER BY month DESC");

// Best Selling Sweets
$bestSelling = mysqli_query($conn,
"SELECT s.sweet_name,
        SUM(oi.quantity) AS sold_quantity
 FROM order_items oi
 INNER JOIN sweets s
 ON oi.sweet_id = s.sweet_id
 GROUP BY oi.sweet_id
 ORDER BY sold_quantity DESC
 LIMIT 5");

// Recent Orders
$recentOrders = mysqli_query($conn,
"SELECT order_id,
        customer_name,
        total_amount,
        order_status
 FROM orders
 ORDER BY order_date DESC
 LIMIT 10");

// Payment Summary
$paymentSummary = mysqli_query($conn,
"SELECT payment_status,
        COUNT(*) AS total
 FROM payments
 GROUP BY payment_status");

?>

<div class="row">

<div class="col-lg-6">

<h4 class="mb-3">Monthly Sales</h4>

<table class="table table-bordered">

<thead class="table-dark">

<tr>

<th>Month</th>
<th>Orders</th>
<th>Sales</th>

</tr>

</thead>

<tbody>

<?php while($row=mysqli_fetch_assoc($monthlySales)){ ?>

<tr>

<td><?php echo $row['month']; ?></td>

<td><?php echo $row['total_orders']; ?></td>

<td>₹<?php echo number_format($row['total_sales'],2); ?></td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

<div class="col-lg-6">

<h4 class="mb-3">Best Selling Sweets</h4>

<table class="table table-bordered">

<thead class="table-success">

<tr>

<th>Sweet</th>

<th>Sold Quantity</th>

</tr>

</thead>

<tbody>

<?php while($row=mysqli_fetch_assoc($bestSelling)){ ?>

<tr>

<td><?php echo htmlspecialchars($row['sweet_name']); ?></td>

<td><?php echo $row['sold_quantity']; ?></td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

<hr>

<div class="row">

<div class="col-lg-8">

<h4 class="mb-3">Recent Orders</h4>

<table class="table table-bordered">

<thead class="table-primary">

<tr>

<th>Order ID</th>
<th>Customer</th>
<th>Total</th>
<th>Status</th>

</tr>

</thead>

<tbody>

<?php while($row=mysqli_fetch_assoc($recentOrders)){ ?>

<tr>

<td>#<?php echo $row['order_id']; ?></td>

<td><?php echo htmlspecialchars($row['customer_name']); ?></td>

<td>₹<?php echo number_format($row['total_amount'],2); ?></td>

<td><?php echo htmlspecialchars($row['order_status']); ?></td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

<div class="col-lg-4">

<h4 class="mb-3">Payment Summary</h4>

<table class="table table-bordered">

<thead class="table-warning">

<tr>

<th>Status</th>
<th>Total</th>

</tr>

</thead>

<tbody>

<?php while($row=mysqli_fetch_assoc($paymentSummary)){ ?>

<tr>

<td><?php echo htmlspecialchars($row['payment_status']); ?></td>

<td><?php echo $row['total']; ?></td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

<hr>

<div class="row mt-4 no-print">

    <div class="col-md-6">

        <a href="../dashboard.php" class="btn btn-secondary">
            ← Back to Dashboard
        </a>

    </div>

    <div class="col-md-6 text-end">

        <button onclick="window.print();" class="btn btn-primary">
            🖨 Print Report
        </button>

    </div>

</div>

<hr>

<div class="text-center mt-4">

    <p class="text-muted mb-1">
        <strong>Sweet Shop - Online Sweet Shopping & Storage System</strong>
    </p>

    <p class="text-muted">
        Sales Report generated on
        <?php echo date("d-m-Y h:i A"); ?>
    </p>

    <p class="text-muted">
        This is a computer-generated report.
    </p>

</div>

</div>

<style>

@media print{

.no-print{
    display:none !important;
}

body{
    background:#ffffff;
}

.container{
    width:100%;
    max-width:100%;
}

}

</style>

</body>

</html>

