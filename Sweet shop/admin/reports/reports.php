<?php
session_start();
include("../../config/db.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1">

<title>Reports - Sweet Shop</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>

body {
    background:#f5f5f5;
}

.navbar {
    background:#8B0000;
}

.navbar-brand {
    color:white;
    font-weight:bold;
    font-size:26px;
}

.navbar-brand:hover {
    color:#FFD700;
}

.report-card {
    border:0;
    border-radius:15px;
    transition:.3s;
}

.report-card:hover {
    transform:translateY(-5px);
    box-shadow:0 10px 25px rgba(0,0,0,.15);
}

.report-icon {
    font-size:55px;
}

</style>

</head>

<body>


<!-- NAVBAR -->

<nav class="navbar">

<div class="container">

<a href="../dashboard.php"
   class="navbar-brand">

🍬 Sweet Shop

</a>

<a href="../dashboard.php"
   class="btn btn-light">

<i class="bi bi-arrow-left"></i>
Dashboard

</a>

</div>

</nav>


<!-- PAGE -->

<div class="container py-5">

<div class="text-center mb-5">

<h1 class="fw-bold text-danger">

<i class="bi bi-bar-chart-line-fill"></i>

Reports

</h1>

<p class="text-muted">

Select a report to generate

</p>

</div>


<div class="row g-4">


<!-- SALES REPORT -->

<div class="col-md-4">

<div class="card report-card shadow h-100 text-center">

<div class="card-body p-4">

<i class="bi bi-graph-up-arrow text-success report-icon"></i>

<h4 class="mt-3">

Sales Report

</h4>

<p class="text-muted">

View total sales and daily sales.

</p>

<a href="sale_reports.php"
   class="btn btn-success">

    <i class="bi bi-eye"></i>
    View Sales Report

</a>


</a>

</div>

</div>

</div>


<!-- ORDER REPORT -->

<div class="col-md-4">

<div class="card report-card shadow h-100 text-center">

<div class="card-body p-4">

<i class="bi bi-box-seam text-primary report-icon"></i>

<h4 class="mt-3">

Order Report

</h4>

<p class="text-muted">

View order status and order details.

</p>

<a
href="order_report.php?report=orders"
class="btn btn-primary">

<i class="bi bi-eye"></i>

View Order Report

</a>

</div>

</div>

</div>


<!-- PAYMENT REPORT -->

<div class="col-md-4">

<div class="card report-card shadow h-100 text-center">

<div class="card-body p-4">

<i class="bi bi-credit-card text-warning report-icon"></i>

<h4 class="mt-3">

Payment Report

</h4>

<p class="text-muted">

View payment method and payment status.

</p>

<a
href="reports.php?report=payments"
class="btn btn-warning">

<i class="bi bi-eye"></i>

View Payment Report

</a>

</div>

</div>

</div>


<!-- PRODUCT REPORT -->

<div class="col-md-4">

<div class="card report-card shadow h-100 text-center">

<div class="card-body p-4">

<i class="bi bi-cup-straw text-danger report-icon"></i>

<h4 class="mt-3">

Sweet/Product Report

</h4>

<p class="text-muted">

View best-selling sweets and quantities.

</p>

<a
href="reports.php?report=products"
class="btn btn-danger">

<i class="bi bi-eye"></i>

View Product Report

</a>

</div>

</div>

</div>


<!-- CUSTOMER REPORT -->

<div class="col-md-4">

<div class="card report-card shadow h-100 text-center">

<div class="card-body p-4">

<i class="bi bi-people-fill text-info report-icon"></i>

<h4 class="mt-3">

Customer Report

</h4>

<p class="text-muted">

View customer orders and spending.

</p>

<a
href="reports.php?report=customers"
class="btn btn-info">

<i class="bi bi-eye"></i>

View Customer Report

</a>

</div>

</div>

</div>


<!-- PROFIT REPORT -->

<div class="col-md-4">

<div class="card report-card shadow h-100 text-center">

<div class="card-body p-4">

<i class="bi bi-cash-stack text-success report-icon"></i>

<h4 class="mt-3">

Profit Report

</h4>

<p class="text-muted">

View sales, cost and profit.

</p>

<a
href="reports.php?report=profit"
class="btn btn-success">

<i class="bi bi-eye"></i>

View Profit Report

</a>

</div>

</div>

</div>


</div>

</div>

</body>

</html>