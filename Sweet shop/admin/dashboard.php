    <?php
    session_start();
    include("../config/db.php");
    // ==========================================
    // ADMIN LOGIN CHECK
    // ==========================================
    if (!isset($_SESSION['admin_id'])) {

        header("Location: login.php");
        exit();
    }
    ?>

    <!DOCTYPE html>

    <html lang="en">

    <head>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1">

    <title>Admin Dashboard</title>

    <!-- Bootstrap -->

    <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet">

    <!-- Bootstrap Icons -->

    <link
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
    rel="stylesheet">


    <style>
    body {

        background:#f5f5f5;

    }

    /* ================= SIDEBAR ================= */

    .sidebar {

        width:250px;

        height:100vh;

        position:fixed;

        left:0;

        top:0;

        background:#8b0000;

        overflow-y:auto;

    }


    .sidebar h3 {

        color:white;

        text-align:center;

        padding:20px;

    }


    .sidebar a {

        display:block;

        color:white;

        text-decoration:none;

        padding:15px 20px;

        font-size:17px;

    }


    .sidebar a:hover {

        background:#a30000;

    }


    /* ================= MAIN ================= */

    .main {

        margin-left:250px;

    }


    /* ================= TOP BAR ================= */

    .topbar {

        background:white;

        padding:15px 25px;

        box-shadow:0 2px 10px rgba(0,0,0,.1);

    }


    /* ================= CONTENT ================= */

    .content {

        padding:30px;

    }


    /* ================= CARDS ================= */

    .dashboard-card {

        transition:.3s;

    }


    .dashboard-card:hover {

        transform:translateY(-5px);

    }


    /* ================= MOBILE ================= */

    @media(max-width:768px) {

        .sidebar {

            width:100%;

            height:auto;

            position:relative;

        }

        .main {

            margin-left:0;

        }

    }

    </style>
    </head>
    <body>
    <!-- ================================================= -->
    <!-- SIDEBAR -->
    <!-- ================================================= -->

    <div class="sidebar">

        <h3>
            🍬 Sweet Shop
        </h3>

        <a href="dashboard.php">
            <i class="bi bi-speedometer2"></i>
            Dashboard
        </a>

        <a href="sweets/view.php">
            <i class="bi bi-box"></i>
            Manage Sweets
        </a>

        <a href="category/view.php">
            <i class="bi bi-grid"></i>
            Categories
        </a>

        <a href="supplier/view.php">
            <i class="bi bi-truck"></i>
            Suppliers
        </a>

        <a href="stock/view.php">
            <i class="bi bi-archive"></i>
            Stock
        </a>

        <a href="orders/view.php">
            <i class="bi bi-bag-check"></i>
            Orders
        </a>

        <a href="payments/view.php">
            <i class="bi bi-credit-card"></i>
            Payments
        </a>

        <a href="customers/view.php">
            <i class="bi bi-people"></i>
            Customers
        </a>

        <a href="reports/reports.php">
            <i class="bi bi-bar-chart-line-fill"></i>
            Reports
        </a>

        <a href="logout.php">
            <i class="bi bi-box-arrow-right"></i>
            Logout
        </a>

    </div>

    <!-- ================================================= -->
    <!-- MAIN -->
    <!-- ================================================= -->

    <div class="main">

    <!-- ================================================= -->
    <!-- TOP BAR -->
    <!-- ================================================= -->

    <div class="topbar d-flex justify-content-between align-items-center">

        <h3>

            Admin Dashboard

        </h3>

        <div>

            Welcome,

            <b>
                <?php
                echo htmlspecialchars(
                    $_SESSION['admin_name'] ?? 'Admin'
                );

                ?>
            </b>

        </div>

    </div>

   
    <!-- CONTENT -->
    <div class="content">

    <h2 class="mb-4">
        Dashboard Overview
    </h2>

    <?php

   
    // DASHBOARD COUNTS
    
    $totalCustomers = 0;

    $totalSweets = 0;

    $totalCategories = 0;

    $totalOrders = 0;

    $totalSuppliers = 0;

    $totalPayments = 0;

    // Customers

    $result = mysqli_query($conn,"SELECT COUNT(*) AS total FROM customer_detail");

    if ($result) 
    {
        $row = mysqli_fetch_assoc($result);
        $totalCustomers = $row['total'];
    }
    // Sweets
    $result = mysqli_query($conn,"SELECT COUNT(*) AS total FROM sweets");

    if ($result) 
    {
        $row = mysqli_fetch_assoc($result);
        $totalSweets = $row['total'];
    }

    // Categories

    $result = mysqli_query ( $conn, "SELECT COUNT(*) AS total FROM category");

    if ($result) 
    {
        $row = mysqli_fetch_assoc($result);
        $totalCategories = $row['total'];
    }

    // Orders

    $result = mysqli_query($conn,"SELECT COUNT(*) AS total FROM `order`");

    if ($result) {

        $row = mysqli_fetch_assoc($result);

        $totalOrders = $row['total'];

    }

    // Suppliers

    $result = mysqli_query($conn,"SELECT COUNT(*) AS total FROM supplier_detail");

    if ($result) {

        $row = mysqli_fetch_assoc($result);

        $totalSuppliers = $row['total'];

    }
    // Payments

    $result = mysqli_query($conn,"SELECT COUNT(*) AS total FROM payment");

    if ($result) {

        $row = mysqli_fetch_assoc($result);

        $totalPayments = $row['total'];

    }

    ?>

    <!-- DASHBOARD CARDS -->


    <div class="row">

    <!-- CUSTOMERS -->

    <div class="col-lg-4 col-md-6 mb-4">
    <div class="card dashboard-card border-0 shadow bg-primary text-white">
    <div class="card-body">
    <h5>
    <i class="bi bi-people"></i>
    Total Customers
    </h5>
             <h1>
                <?php echo $totalCustomers; ?>
            </h1>
    </div>
    </div>
    </div>



    <!-- SWEETS -->

    <div class="col-lg-4 col-md-6 mb-4">

    <div class="card dashboard-card border-0 shadow bg-success text-white">

    <div class="card-body">

    <h5>

    <i class="bi bi-box"></i>

    Total Sweets

    </h5>

    <h1>

    <?php echo $totalSweets; ?>

    </h1>

    </div>

    </div>

    </div>



    <!-- CATEGORIES -->

    <div class="col-lg-4 col-md-6 mb-4">

    <div class="card dashboard-card border-0 shadow bg-warning text-dark">

    <div class="card-body">

    <h5>

    <i class="bi bi-grid"></i>

    Total Categories

    </h5>

    <h1>

    <?php echo $totalCategories; ?>

    </h1>

    </div>

    </div>

    </div>



    <!-- ORDERS -->

    <div class="col-lg-4 col-md-6 mb-4">

    <div class="card dashboard-card border-0 shadow bg-danger text-white">

    <div class="card-body">

    <h5>

    <i class="bi bi-bag-check"></i>

    Total Orders

    </h5>

    <h1>

    <?php echo $totalOrders; ?>

    </h1>

    </div>

    </div>

    </div>



    <!-- SUPPLIERS -->

    <div class="col-lg-4 col-md-6 mb-4">

    <div class="card dashboard-card border-0 shadow bg-info text-dark">

    <div class="card-body">

    <h5>

    <i class="bi bi-truck"></i>

    Total Suppliers

    </h5>

    <h1>

    <?php echo $totalSuppliers; ?>

    </h1>

    </div>

    </div>

    </div>



    <!-- PAYMENTS -->

    <div class="col-lg-4 col-md-6 mb-4">

    <div class="card dashboard-card border-0 shadow bg-dark text-white">

    <div class="card-body">

    <h5>

    <i class="bi bi-credit-card"></i>

    Total Payments

    </h5>

    <h1>

    <?php echo $totalPayments; ?>

    </h1>

    </div>

    </div>

    </div>


    </div>



    <!-- RECENT ORDERS -->

    <div class="card shadow mt-4">


    <div class="card-header bg-primary text-white">

    <h4 class="mb-0">

    <i class="bi bi-clock-history"></i>

    Recent Orders

    </h4>

    </div>



    <div class="card-body">


    <div class="table-responsive">


    <table class="table table-bordered table-hover align-middle">
8

    <thead class="table-dark">


    <tr>

    <th>Order ID</th>

    <th>Customer</th>

    <th>Total Amount</th>

    <th>Payment</th>

    <th>Payment Status</th>

    <th>Order Status</th>

    <th>Order Date</th>

    <th>Action</th>

    </tr>


    </thead>



    <tbody>


    <?php
 $sql="SELECT o.order_id,c.name AS customer_name,o.total_amount,o.order_status,o.order_date,
    p.payment_method,p.payment_status FROM `order` o
    INNER JOIN customer_detail c ON o.customer_id = c.customer_id 
    LEFT JOIN payment p ON o.order_id = p.order_id
    ORDER BY o.order_id DESC LIMIT 10";
    $query = mysqli_query($conn,$sql);
    if (!$query) {

        die(
            "Recent Order SQL Error: "
            . mysqli_error($conn)
        );
    }
    if (mysqli_num_rows($query) > 0) {
        while (
            $row =
            mysqli_fetch_assoc($query)
        ) {
            $paymentStatus = strtolower(
                trim(
                    $row['payment_status']
                    ?? 'pending'
                )
            );
            $orderStatus = strtolower(
                trim(
                    $row['order_status']
                    ?? 'pending'
                )
            );

    ?>
    <tr>

    <td>
    <strong>

    #<?php

    echo $row['order_id'];

    ?>

    </strong>

    </td>


    <td>

    <?php

    echo (
        $row['customer_name']
    );

    ?>

    </td>


    <td>

    <strong>

    ₹<?php

    echo ($row['total_amount']);;

    ?>

    </strong>
    </td>

    <td>
    <?php
    echo ($row['payment_method']);
    ?>

    </td>

    <td>

    <?php
    if ($paymentStatus == "paid")
         {

        echo "<span class='badge bg-success'> Paid</span>";

        } 
    elseif ($paymentStatus == "pending")
         {echo "<span class='badge bg-warning text-dark'>Pending</span>";

    } 
    else {
        echo "<span class='badge bg-secondary'> " .($row['payment_status']). "</span>";

    }


    ?>

    </td>



    <!-- ORDER STATUS -->

    <td>


    <?php


    switch ($orderStatus) {


        case "pending":
            echo "<span class='badge bg-warning text-dark'>Pending </span>";
            break;

        case "processing":
            echo "<span class='badge bg-info text-dark'>Processing</span>";
             break;

        case "delivered":
            echo "<span class='badge bg-success'>Delivered</span>";
            break;

        case "cancelled":
            echo "<span class='badge bg-danger'> Cancelled</span>";
            break;
        default:


            echo " <span class='badge bg-secondary'>".($row['order_status']). "</span>";

    }


    ?>

    </td>

    <td>

    <?php

    echo htmlspecialchars(
        $row['order_date']
    );

    ?>

    </td>



    <!-- ACTION -->

    <td>

    <a

    href="orders/details.php?id=<?php echo $row['order_id']; ?>"

    class="btn btn-sm btn-primary mb-1">

    <i class="bi bi-eye"></i>

    View

    </a>



    <a

    href="orders/update_order.php?id=<?php echo $row['order_id']; ?>"

    class="btn btn-sm btn-warning mb-1">

    <i class="bi bi-pencil-square"></i>

    Update

    </a>


    </td>


    </tr>


    <?php

        }

    } else {


    ?>


    <tr>

    <td colspan="8"
        class="text-center text-danger">

    No Orders Found

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

    <!-- LOW STOCK + SALES -->

    <div class="row mt-4">


    <!-- LOW STOCK -->

    <div class="col-lg-6">


    <div class="card shadow">


    <div class="card-header bg-danger text-white">

    <h5 class="mb-0">

    <i class="bi bi-exclamation-triangle"></i>

    Low Stock Alert

    </h5>

    </div>



    <div class="card-body">


    <?php


    $stock = mysqli_query(
        $conn,

        "

        SELECT

            sweets.sweet_name,

            stock.quantity_in_stock
            AS stock_quantity

        FROM sweets

        INNER JOIN stock

            ON sweets.sweet_id =
            stock.sweet_id

        WHERE
            stock.quantity_in_stock <= 10

        ORDER BY
            stock.quantity_in_stock ASC

        "
    );


    if (!$stock) {

        echo "

        <div class='alert alert-danger'>

            Stock Error:

            "
            . htmlspecialchars(
                mysqli_error($conn)
            )
            . "

        </div>

        ";

    } elseif (
        mysqli_num_rows($stock) > 0
    ) {


    ?>


    <table class="table table-bordered">


    <thead>

    <tr>

    <th>Sweet</th>

    <th>Stock</th>

    </tr>

    </thead>


    <tbody>


    <?php


    while (
        $stockRow =
        mysqli_fetch_assoc($stock)
    ) {


    ?>


    <tr>


    <td>

    <?php

    echo htmlspecialchars(
        $stockRow['sweet_name']
    );

    ?>

    </td>



    <td>

    <span class="badge bg-danger">

    <?php

    echo $stockRow['stock_quantity'];

    ?>

    </span>

    </td>


    </tr>


    <?php

    }

    ?>


    </tbody>

    </table>


    <?php


    } else {


    echo "

    <div class='alert alert-success'>

    All products are sufficiently stocked.

    </div>

    ";


    }


    ?>


    </div>

    </div>

    </div>



    <!-- SALES SUMMARY -->

    <div class="col-lg-6">


    <div class="card shadow">


    <div class="card-header bg-success text-white">

    <h5 class="mb-0">

    <i class="bi bi-currency-rupee"></i>

    Sales Summary

    </h5>

    </div>



    <div class="card-body">


    <?php


    $sales = mysqli_query(
        $conn,

        "

        SELECT

            COALESCE(
                SUM(total_amount),
                0
            ) AS total

        FROM `order`

        WHERE LOWER(order_status)
            = 'delivered'

        "
    );


    if (!$sales) {


    $totalSales = 0;


    } else {


    $data =
    mysqli_fetch_assoc($sales);


    $totalSales =
    $data['total'] ?? 0;


    }


    ?>


    <h2 class="text-success">


    ₹<?php

    echo number_format(
        $totalSales,
        2
    );

    ?>


    </h2>


    <p class="mb-0">

    Total Delivered Sales

    </p>


    </div>

    </div>

    </div>


    </div>

    <!-- FOOTER -->

    <footer class="text-center mt-5 mb-3">

    <hr>

    <p>

    © 2026 krishna sweets

    <br>

    Admin Dashboard

    </p>

    </footer>


    </div>


    </div>



    <!-- Bootstrap JS -->

    <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    </script>


    </body>

    </html>