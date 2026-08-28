<?php

session_start();
include("config/db.php");

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);


// ==================================================
// CHECK CUSTOMER LOGIN
// ==================================================

if (!isset($_SESSION['customer_id'])) {

    header("Location: login.php");
    exit();

}

$customer_id = $_SESSION['customer_id'];


// ==================================================
// GET CUSTOMER DETAILS
// ==================================================

$customerQuery = mysqli_query($conn, "
    SELECT *
    FROM customer_detail
    WHERE customer_id = '$customer_id'
    LIMIT 1
");

$customer = mysqli_fetch_assoc($customerQuery);


// ==================================================
// GET ORDERS
// ==================================================

$orderQuery = mysqli_query($conn, "

    SELECT

        o.order_id,
        o.order_date,
        o.total_amount,
        o.order_status,

        p.payment_method,
        p.payment_status,
        p.transaction_id

    FROM `order` o

    LEFT JOIN payment p
        ON o.order_id = p.order_id

    WHERE o.customer_id = '$customer_id'

    ORDER BY o.order_id DESC

");

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1">

<title>My Orders - Sweet Shop</title>


<!-- Bootstrap -->

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">


<!-- Bootstrap Icons -->

<link
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
rel="stylesheet">


<style>

/* ==========================================
   BODY
========================================== */

body {

    margin: 0;

    background:
        linear-gradient(
            135deg,
            #fff5f5,
            #ffe0e0
        );

    min-height: 100vh;

    font-family: Arial, sans-serif;

}


/* ==========================================
   NAVBAR
========================================== */

.navbar {

    background: #8B0000;

}


.navbar-brand {

    color: white !important;

    font-size: 23px;

    font-weight: bold;

}


.nav-link {

    color: white !important;

}


.nav-link:hover {

    color: #ffd700 !important;

}


/* ==========================================
   PAGE
========================================== */

.page-container {

    width: 95%;

    max-width: 1200px;

    margin: auto;

}


/* ==========================================
   TITLE
========================================== */

.page-title {

    color: #8B0000;

    font-weight: bold;

}


.page-subtitle {

    color: #777;

}


/* ==========================================
   ORDER CARD
========================================== */

.order-card {

    background: white;

    border-radius: 15px;

    margin-bottom: 25px;

    box-shadow:
        0 8px 25px
        rgba(0,0,0,0.10);

    overflow: hidden;

}


/* ==========================================
   ORDER HEADER
========================================== */

.order-header {

    background: #8B0000;

    color: white;

    padding: 18px 22px;

}


.order-id {

    font-size: 20px;

    font-weight: bold;

}


/* ==========================================
   ORDER BODY
========================================== */

.order-body {

    padding: 22px;

}


/* ==========================================
   INFO BOX
========================================== */

.info-box {

    background: #f8f9fa;

    border-radius: 10px;

    padding: 15px;

}


.info-title {

    color: #555;

    font-size: 14px;

}


.info-value {

    font-weight: bold;

    margin-top: 3px;

}


/* ==========================================
   TOTAL
========================================== */

.total-amount {

    color: #198754;

    font-size: 22px;

    font-weight: bold;

}


/* ==========================================
   BADGES
========================================== */

.status-pending {

    background: #ffc107;

    color: #000;

}


.status-delivered {

    background: #198754;

    color: white;

}


.status-cancelled {

    background: #dc3545;

    color: white;

}


.status-paid {

    background: #198754;

    color: white;

}


.status-unpaid {

    background: #ffc107;

    color: #000;

}


/* ==========================================
   EMPTY ORDERS
========================================== */

.empty-box {

    background: white;

    padding: 60px 20px;

    border-radius: 15px;

    text-align: center;

    box-shadow:
        0 8px 25px
        rgba(0,0,0,0.10);

}


.empty-icon {

    font-size: 70px;

}


/* ==========================================
   BUTTONS
========================================== */

.btn-shop {

    background: #ffc107;

    color: black;

    font-weight: bold;

}


.btn-shop:hover {

    background: #ffca2c;

}


</style>

</head>


<body>


<!-- ==================================================
     NAVBAR
================================================== -->

<nav class="navbar navbar-expand-lg">

<div class="container">


    <a
    class="navbar-brand"
    href="index.php">

        🍬 Sweet Shop

    </a>


    <button
    class="navbar-toggler"
    type="button"
    data-bs-toggle="collapse"
    data-bs-target="#navbarMenu">

        <span class="navbar-toggler-icon"></span>

    </button>


    <div
    class="collapse navbar-collapse"
    id="navbarMenu">


        <ul class="navbar-nav ms-auto">


            <li class="nav-item">

                <a
                class="nav-link"
                href="index.php">

                    <i class="bi bi-house"></i>

                    Home

                </a>

            </li>


            <li class="nav-item">

                <a
                class="nav-link"
                href="sweets.php">

                    <i class="bi bi-shop"></i>

                    Sweets

                </a>

            </li>


            <li class="nav-item">

                <a
                class="nav-link"
                href="cart.php">

                    <i class="bi bi-cart"></i>

                    Cart

                </a>

            </li>


            <li class="nav-item">

                <a
                class="nav-link active"
                href="my_orders.php">

                    <i class="bi bi-bag-check"></i>

                    My Orders

                </a>

            </li>


        </ul>


    </div>

</div>

</nav>



<!-- ==================================================
     MAIN CONTENT
================================================== -->

<div class="page-container py-5">


    <!-- TITLE -->

    <div class="text-center mb-5">

        <h1 class="page-title">

            <i class="bi bi-bag-check"></i>

            My Orders

        </h1>


        <p class="page-subtitle">

            Welcome,

            <strong>

                <?php

                echo htmlspecialchars(
                    $customer['name']
                );

                ?>

            </strong>

            — Here you can view your orders.

        </p>

    </div>



    <?php

    // ==================================================
    // CHECK ORDERS
    // ==================================================

    if (mysqli_num_rows($orderQuery) == 0) {

    ?>


        <!-- ==================================================
             NO ORDERS
        ================================================== -->

        <div class="empty-box">


            <div class="empty-icon">

                🛒

            </div>


            <h3 class="mt-3">

                No Orders Yet

            </h3>


            <p class="text-muted">

                You haven't placed any orders yet.

            </p>


            <a
            href="sweets.php"
            class="btn btn-shop px-4">

                <i class="bi bi-shop"></i>

                Start Shopping

            </a>


        </div>


    <?php

    }

    // ==================================================
    // SHOW ORDERS
    // ==================================================

    else {

        while ($order = mysqli_fetch_assoc($orderQuery)) {

            // Order status
            $orderStatus = $order['order_status'];

            // Payment status
            $paymentStatus = $order['payment_status'];

    ?>


        <!-- ==================================================
             ORDER CARD
        ================================================== -->

        <div class="order-card">


            <!-- ORDER HEADER -->

            <div class="order-header">


                <div class="row align-items-center">


                    <div class="col-md-6">

                        <div class="order-id">

                            <i class="bi bi-receipt"></i>

                            Order #<?php

                            echo $order['order_id'];

                            ?>

                        </div>


                        <small>

                            Order Date:

                            <?php

                            echo date(
                                "d-m-Y",
                                strtotime(
                                    $order['order_date']
                                )
                            );

                            ?>

                        </small>

                    </div>


                    <div class="col-md-6 text-md-end mt-2 mt-md-0">


                        <?php

                        if (
                            strtolower(
                                $orderStatus
                            ) == "delivered"
                        ) {

                        ?>

                            <span
                            class="badge status-delivered p-2">

                                <i class="bi bi-check-circle"></i>

                                Delivered

                            </span>

                        <?php

                        }
                        elseif (
                            strtolower(
                                $orderStatus
                            ) == "cancelled"
                        ) {

                        ?>

                            <span
                            class="badge status-cancelled p-2">

                                <i class="bi bi-x-circle"></i>

                                Cancelled

                            </span>

                        <?php

                        }
                        else {

                        ?>

                            <span
                            class="badge status-pending p-2">

                                <i class="bi bi-clock"></i>

                                <?php

                                echo htmlspecialchars(
                                    $orderStatus
                                );

                                ?>

                            </span>

                        <?php

                        }

                        ?>


                    </div>


                </div>


            </div>



            <!-- ORDER BODY -->

            <div class="order-body">


                <div class="row g-3">


                    <!-- TOTAL -->

                    <div class="col-md-3">


                        <div class="info-box">

                            <div class="info-title">

                                Total Amount

                            </div>


                            <div class="total-amount">

                                ₹<?php

                                echo number_format(
                                    $order['total_amount'],
                                    2
                                );

                                ?>

                            </div>

                        </div>


                    </div>



                    <!-- PAYMENT METHOD -->

                    <div class="col-md-3">


                        <div class="info-box">

                            <div class="info-title">

                                Payment Method

                            </div>


                            <div class="info-value">

                                <?php

                                echo htmlspecialchars(
                                    $order['payment_method']
                                    ?? 'Not Available'
                                );

                                ?>

                            </div>

                        </div>


                    </div>



                    <!-- PAYMENT STATUS -->

                    <div class="col-md-3">


                        <div class="info-box">

                            <div class="info-title">

                                Payment Status

                            </div>


                            <div class="mt-1">


                                <?php

                                if (
                                    strtolower(
                                        $paymentStatus ?? ''
                                    ) == "paid"
                                ) {

                                ?>

                                    <span
                                    class="badge status-paid">

                                        <i class="bi bi-check-circle"></i>

                                        Paid

                                    </span>

                                <?php

                                }
                                else {

                                ?>

                                    <span
                                    class="badge status-unpaid">

                                        <i class="bi bi-clock"></i>

                                        <?php

                                        echo htmlspecialchars(
                                            $paymentStatus
                                            ?? 'Pending'
                                        );

                                        ?>

                                    </span>

                                <?php

                                }

                                ?>


                            </div>

                        </div>


                    </div>



                    <!-- TRANSACTION -->

                    <div class="col-md-3">


                        <div class="info-box">

                            <div class="info-title">

                                Transaction ID

                            </div>


                            <div class="info-value">

                                <?php

                                if (
                                    !empty(
                                        $order['transaction_id']
                                    )
                                ) {

                                    echo htmlspecialchars(
                                        $order['transaction_id']
                                    );

                                }
                                else {

                                    echo "Not Available";

                                }

                                ?>

                            </div>

                        </div>


                    </div>


                </div>



                <!-- ==================================================
                     BUTTONS
                ================================================== -->

                <div
                class="d-flex justify-content-between
                       align-items-center mt-4 flex-wrap gap-2">


                    <div>

                        <a
                        href="sweets.php"
                        class="btn btn-shop">

                            <i class="bi bi-shop"></i>

                            Continue Shopping

                        </a>

                    </div>


                    <div>

                        <a
                        href="order_details.php?order_id=<?php
                        echo $order['order_id'];
                        ?>"
                        class="btn btn-primary">

                            <i class="bi bi-eye"></i>

                            View Details

                        </a>

                    </div>


                </div>


            </div>


        </div>


    <?php

        }

    }

    ?>


</div>



<!-- ==================================================
     FOOTER
================================================== -->

<footer class="text-center py-4 text-muted">

    <p class="mb-0">

        🍬 Sweet Shop ©

        <?php echo date("Y"); ?>

        | All Rights Reserved

    </p>

</footer>



<script
src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>


</body>

</html>