<?php

session_start();
include("../../config/db.php");

// ==========================================
// ADMIN LOGIN CHECK
// ==========================================

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}


// ==========================================
// DATE FILTER
// ==========================================

$from_date = $_GET['from_date'] ?? date('Y-m-01');
$to_date   = $_GET['to_date'] ?? date('Y-m-d');


// ==========================================
// TOTAL ORDERS
// ==========================================

$totalOrderQuery = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM `order`
     WHERE order_date BETWEEN '$from_date' AND '$to_date'"
);

if (!$totalOrderQuery) {
    die("Total Order Query Error: " . mysqli_error($conn));
}

$totalOrderData = mysqli_fetch_assoc($totalOrderQuery);

$total_orders = $totalOrderData['total'];


// ==========================================
// TOTAL SALES
// Cancelled orders excluded
// ==========================================

$totalSalesQuery = mysqli_query(
    $conn,
    "SELECT COALESCE(SUM(total_amount), 0) AS total
     FROM `order`
     WHERE order_date BETWEEN '$from_date' AND '$to_date'
     AND LOWER(order_status) != 'cancelled'"
);

if (!$totalSalesQuery) {
    die("Total Sales Query Error: " . mysqli_error($conn));
}

$totalSalesData = mysqli_fetch_assoc($totalSalesQuery);

$total_sales = $totalSalesData['total'];


// ==========================================
// DELIVERED ORDERS
// ==========================================

$deliveredQuery = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM `order`
     WHERE order_date BETWEEN '$from_date' AND '$to_date'
     AND LOWER(order_status) = 'delivered'"
);

$deliveredData = mysqli_fetch_assoc($deliveredQuery);

$delivered_orders = $deliveredData['total'];


// ==========================================
// CANCELLED ORDERS
// ==========================================

$cancelledQuery = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM `order`
     WHERE order_date BETWEEN '$from_date' AND '$to_date'
     AND LOWER(order_status) = 'cancelled'"
);

$cancelledData = mysqli_fetch_assoc($cancelledQuery);

$cancelled_orders = $cancelledData['total'];


// ==========================================
// AVERAGE ORDER VALUE
// ==========================================

$averageQuery = mysqli_query(
    $conn,
    "SELECT COALESCE(AVG(total_amount), 0) AS average
     FROM `order`
     WHERE order_date BETWEEN '$from_date' AND '$to_date'
     AND LOWER(order_status) != 'cancelled'"
);

$averageData = mysqli_fetch_assoc($averageQuery);

$average_order = $averageData['average'];


// ==========================================
// DAILY SALES REPORT
// ==========================================

$dailyQuery = mysqli_query(
    $conn,
    "SELECT
        order_date,
        COUNT(order_id) AS total_orders,

        COALESCE(
            SUM(
                CASE
                    WHEN LOWER(order_status) != 'cancelled'
                    THEN total_amount
                    ELSE 0
                END
            ),
            0
        ) AS total_sales

     FROM `order`

     WHERE order_date BETWEEN '$from_date' AND '$to_date'

     GROUP BY order_date

     ORDER BY order_date DESC"
);

if (!$dailyQuery) {
    die("Daily Sales Query Error: " . mysqli_error($conn));
}


// ==========================================
// MONTHLY SALES
// ==========================================

$monthlyQuery = mysqli_query(
    $conn,
    "SELECT
        DATE_FORMAT(order_date, '%Y-%m') AS month,
        COUNT(order_id) AS total_orders,

        COALESCE(
            SUM(
                CASE
                    WHEN LOWER(order_status) != 'cancelled'
                    THEN total_amount
                    ELSE 0
                END
            ),
            0
        ) AS total_sales

     FROM `order`

     WHERE order_date BETWEEN '$from_date' AND '$to_date'

     GROUP BY DATE_FORMAT(order_date, '%Y-%m')

     ORDER BY month DESC"
);

if (!$monthlyQuery) {
    die("Monthly Sales Query Error: " . mysqli_error($conn));
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1">

<title>Sales Report - Sweet Shop</title>


<!-- Bootstrap -->

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">


<!-- Bootstrap Icons -->

<link
rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">


<style>

body {
    background:#f5f5f5;
}


/* Navbar */

.navbar {
    background:#8B0000;
}

.navbar-brand {
    color:white;
    font-size:26px;
    font-weight:bold;
}

.navbar-brand:hover {
    color:#FFD700;
}


/* Report Cards */

.report-card {
    border:none;
    border-radius:15px;
    box-shadow:0 4px 12px rgba(0,0,0,.12);
}

.report-card h2 {
    font-weight:bold;
}


/* Report Area */

.report-area {
    background:white;
    padding:25px;
    border-radius:15px;
}


/* Print */

@media print {

    .no-print {
        display:none !important;
    }

    body {
        background:white;
    }

    .report-area {
        box-shadow:none;
        padding:0;
    }

}

</style>

</head>


<body>


<!-- ==========================================
     NAVBAR
========================================== -->

<nav class="navbar mb-4 no-print">

<div class="container">

<a
href="../dashboard.php"
class="navbar-brand">

🍬 Sweet Shop

</a>


<div>

<a
href="reports.php"
class="btn btn-light">

<i class="bi bi-arrow-left"></i>

All Reports

</a>


<a
href="../dashboard.php"
class="btn btn-warning">

<i class="bi bi-speedometer2"></i>

Dashboard

</a>

</div>

</div>

</nav>


<!-- ==========================================
     MAIN CONTAINER
========================================== -->

<div class="container pb-5">


<!-- ==========================================
     TITLE
========================================== -->

<div class="text-center mb-4">

<h1 class="fw-bold text-danger">

<i class="bi bi-graph-up-arrow"></i>

Sales Report

</h1>

<p class="text-muted">

Sweet Shop Sales Analysis

</p>

</div>


<!-- ==========================================
     DATE FILTER
========================================== -->

<div class="card shadow mb-4 no-print">

<div class="card-body">

<form method="GET">

<div class="row align-items-end">


<div class="col-md-4">

<label class="form-label fw-bold">

From Date

</label>

<input
type="date"
name="from_date"
class="form-control"
value="<?php echo htmlspecialchars($from_date); ?>"
required>

</div>


<div class="col-md-4">

<label class="form-label fw-bold">

To Date

</label>

<input
type="date"
name="to_date"
class="form-control"
value="<?php echo htmlspecialchars($to_date); ?>"
required>

</div>


<div class="col-md-2">

<button
type="submit"
class="btn btn-danger w-100">

<i class="bi bi-search"></i>

Generate

</button>

</div>


<div class="col-md-2">

<button
type="button"
onclick="window.print()"
class="btn btn-success w-100">

<i class="bi bi-printer"></i>

Print

</button>

</div>


</div>

</form>

</div>

</div>


<!-- ==========================================
     REPORT AREA
========================================== -->

<div class="report-area">


<!-- Report Header -->

<div class="text-center mb-4">

<h2 class="fw-bold">

🍬 Sweet Shop

</h2>

<h4>

Sales Report

</h4>

<p>

<strong>From:</strong>

<?php
echo date(
    'd-m-Y',
    strtotime($from_date)
);
?>

&nbsp;&nbsp;

<strong>To:</strong>

<?php
echo date(
    'd-m-Y',
    strtotime($to_date)
);
?>

</p>

</div>


<!-- ==========================================
     SUMMARY CARDS
========================================== -->

<div class="row g-4 mb-5">


<!-- Total Orders -->

<div class="col-md-3">

<div class="card report-card text-center p-3">

<i class="bi bi-cart-check-fill text-primary"
   style="font-size:40px;"></i>

<h6 class="mt-2">

Total Orders

</h6>

<h2 class="text-primary">

<?php echo $total_orders; ?>

</h2>

</div>

</div>


<!-- Total Sales -->

<div class="col-md-3">

<div class="card report-card text-center p-3">

<i class="bi bi-currency-rupee text-success"
   style="font-size:40px;"></i>

<h6 class="mt-2">

Total Sales

</h6>

<h2 class="text-success">

₹<?php
echo number_format(
    $total_sales,
    2
);
?>

</h2>

</div>

</div>


<!-- Delivered -->

<div class="col-md-3">

<div class="card report-card text-center p-3">

<i class="bi bi-truck text-info"
   style="font-size:40px;"></i>

<h6 class="mt-2">

Delivered Orders

</h6>

<h2 class="text-info">

<?php echo $delivered_orders; ?>

</h2>

</div>

</div>


<!-- Cancelled -->

<div class="col-md-3">

<div class="card report-card text-center p-3">

<i class="bi bi-x-circle-fill text-danger"
   style="font-size:40px;"></i>

<h6 class="mt-2">

Cancelled Orders

</h6>

<h2 class="text-danger">

<?php echo $cancelled_orders; ?>

</h2>

</div>

</div>


</div>


<!-- ==========================================
     AVERAGE ORDER VALUE
========================================== -->

<div class="card shadow mb-5">

<div class="card-body text-center">

<h5>

Average Order Value

</h5>

<h2 class="text-success">

₹<?php
echo number_format(
    $average_order,
    2
);
?>

</h2>

</div>

</div>


<!-- ==========================================
     DAILY SALES REPORT
========================================== -->

<div class="card shadow mb-5">

<div class="card-header bg-primary text-white">

<h4 class="mb-0">

<i class="bi bi-calendar3"></i>

Daily Sales Report

</h4>

</div>


<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead class="table-dark">

<tr>

<th>#</th>

<th>Date</th>

<th>Total Orders</th>

<th>Total Sales</th>

</tr>

</thead>


<tbody>

<?php

$count = 1;

if (mysqli_num_rows($dailyQuery) > 0) {

    while ($row = mysqli_fetch_assoc($dailyQuery)) {

?>

<tr>

<td>

<?php echo $count++; ?>

</td>


<td>

<?php

echo date(
    'd-m-Y',
    strtotime($row['order_date'])
);

?>

</td>


<td>

<?php

echo $row['total_orders'];

?>

</td>


<td class="fw-bold text-success">

₹<?php

echo number_format(
    $row['total_sales'],
    2
);

?>

</td>

</tr>

<?php

    }

} else {

?>

<tr>

<td
colspan="4"
class="text-center text-danger">

No sales found for selected dates.

</td>

</tr>

<?php

}

?>

</tbody>

</table>

</div>

</div>

</div>


<!-- ==========================================
     MONTHLY SALES REPORT
========================================== -->

<div class="card shadow mb-5">

<div class="card-header bg-success text-white">

<h4 class="mb-0">

<i class="bi bi-bar-chart-fill"></i>

Monthly Sales Report

</h4>

</div>


<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead class="table-dark">

<tr>

<th>#</th>

<th>Month</th>

<th>Total Orders</th>

<th>Total Sales</th>

</tr>

</thead>


<tbody>

<?php

$count = 1;

if (mysqli_num_rows($monthlyQuery) > 0) {

    while ($row = mysqli_fetch_assoc($monthlyQuery)) {

?>

<tr>

<td>

<?php echo $count++; ?>

</td>


<td>

<?php

echo date(
    'F Y',
    strtotime($row['month'] . '-01')
);

?>

</td>


<td>

<?php

echo $row['total_orders'];

?>

</td>


<td class="fw-bold text-success">

₹<?php

echo number_format(
    $row['total_sales'],
    2
);

?>

</td>

</tr>

<?php

    }

} else {

?>

<tr>

<td
colspan="4"
class="text-center text-danger">

No monthly sales found.

</td>

</tr>

<?php

}

?>

</tbody>

</table>

</div>

</div>

</div>


<!-- ==========================================
     REPORT FOOTER
========================================== -->

<div class="text-center mt-4">

<p class="text-muted">

Report generated on

<?php echo date('d-m-Y H:i:s'); ?>

</p>

<p class="fw-bold">

Sweet Shop

</p>

<p class="small text-muted">

Online Sweet Shopping & Storage System

</p>

</div>


</div>

</div>


</body>

</html>