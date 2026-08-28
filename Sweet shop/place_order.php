<?php

session_start();

include("config/db.php");

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);


// ==================================================
// 1. CHECK CUSTOMER LOGIN
// ==================================================

if (!isset($_SESSION['customer_id'])) {

    die("ERROR: Customer session is missing.");

}

$customer_id = (int) $_SESSION['customer_id'];


// ==================================================
// 2. CHECK POST REQUEST
// ==================================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header("Location: checkout.php");
    exit();

}


if (
    !isset($_POST['payment_method']) ||
    trim($_POST['payment_method']) == ''
) {

    die("ERROR: Payment method is missing.");

}


$payment_method = trim($_POST['payment_method']);


// ==================================================
// 3. GET CART ITEMS
// ==================================================

$cartSql = "
    SELECT
        c.sweet_id,
        c.quantity,
        s.price
    FROM cart c
    INNER JOIN sweets s
        ON c.sweet_id = s.sweet_id
    WHERE c.customer_id = ?
";

$cartStmt = mysqli_prepare($conn, $cartSql);

mysqli_stmt_bind_param(
    $cartStmt,
    "i",
    $customer_id
);

mysqli_stmt_execute($cartStmt);

$cartResult = mysqli_stmt_get_result($cartStmt);


if (mysqli_num_rows($cartResult) == 0) {

    die("ERROR: Your cart is empty.");

}


// ==================================================
// 4. CALCULATE TOTAL
// ==================================================

$grandTotal = 0;

$cartItems = [];


while ($row = mysqli_fetch_assoc($cartResult)) {

    $sweet_id = (int) $row['sweet_id'];

    $quantity = (int) $row['quantity'];

    $price = (float) $row['price'];


    if ($quantity <= 0) {
        continue;
    }


    $subtotal = $price * $quantity;

    $grandTotal += $subtotal;


    $cartItems[] = [

        "sweet_id" => $sweet_id,

        "quantity" => $quantity,

        "price" => $price

    ];

}


if (empty($cartItems)) {

    die("ERROR: No valid items found in cart.");

}


// ==================================================
// 5. SUPPLIER ID
// ==================================================

// Your order table requires supplier_id.
//
// Make sure supplier ID 1 exists.

$supplier_id = 1;


// ==================================================
// 6. CHECK SUPPLIER
// ==================================================

$supplierSql = "
    SELECT supplier_id
    FROM supplier_detail
    WHERE supplier_id = ?
    LIMIT 1
";

$supplierStmt = mysqli_prepare(
    $conn,
    $supplierSql
);

mysqli_stmt_bind_param(
    $supplierStmt,
    "i",
    $supplier_id
);

mysqli_stmt_execute($supplierStmt);

$supplierResult =
    mysqli_stmt_get_result($supplierStmt);


if (mysqli_num_rows($supplierResult) == 0) {

    die(
        "ERROR: Supplier ID "
        . $supplier_id
        . " does not exist."
    );

}


// ==================================================
// 7. START TRANSACTION
// ==================================================

mysqli_begin_transaction($conn);


try {


    // ==================================================
    // 8. INSERT ORDER
    // ==================================================

    /*
       DO NOT INSERT order_id.

       order_id is AUTO_INCREMENT.
    */

    $orderSql = "
        INSERT INTO `order`
        (
            customer_id,
            supplier_id,
            order_date,
            total_amount,
            order_status
        )
        VALUES
        (
            ?,
            ?,
            CURDATE(),
            ?,
            'Pending'
        )
    ";


    $orderStmt = mysqli_prepare(
        $conn,
        $orderSql
    );


    mysqli_stmt_bind_param(
        $orderStmt,
        "iid",
        $customer_id,
        $supplier_id,
        $grandTotal
    );


    mysqli_stmt_execute($orderStmt);


    // Get generated order ID

    $order_id = mysqli_insert_id($conn);


    if ($order_id <= 0) {

        throw new Exception(
            "Order ID was not generated."
        );

    }


    // ==================================================
    // 9. INSERT ORDER ITEMS
    // ==================================================

    /*
       order_item_id is AUTO_INCREMENT.

       DO NOT INSERT order_item_id.
    */


    $weight = 1000;

    $variant_id = null;


    $itemSql = "
        INSERT INTO order_items
        (
            order_id,
            sweet_id,
            weight,
            variant_id,
            quantity,
            price
        )
        VALUES
        (   
            ?,
            ?,
            ?,
            ?,
            ?,
            ?
        )
    ";


    $itemStmt = mysqli_prepare(
        $conn,
        $itemSql
    );


    foreach ($cartItems as $item) {


        $sweet_id = (int) $item['sweet_id'];

        $quantity = (int) $item['quantity'];

        $price = (float) $item['price'];


        mysqli_stmt_bind_param(
            $itemStmt,
            "iiiisd",
            $order_id,
            $sweet_id,
            $weight,
            $variant_id,
            $quantity,
            $price
        );


        mysqli_stmt_execute($itemStmt);

    }


    // ==================================================
    // 10. PAYMENT DETAILS
    // ==================================================

    $payment_status = "Pending";

    $amount = $grandTotal;


    /*
       transaction_id is NOT NULL and UNIQUE.

       Therefore generate a unique transaction ID.
    */

    if ($payment_method == "Cash on Delivery") {

        $transaction_id =
            "COD-" .
            date("YmdHis") .
            "-" .
            strtoupper(
                bin2hex(
                    random_bytes(3)
                )
            );

    } else {

        $transaction_id =
            "PAY-" .
            date("YmdHis") .
            "-" .
            strtoupper(
                bin2hex(
                    random_bytes(3)
                )
            );

    }


    // ==================================================
    // 11. INSERT PAYMENT
    // ==================================================

    /*
       payment_id is AUTO_INCREMENT.

       DO NOT INSERT payment_id.
    */


    $paymentSql = "
        INSERT INTO payment
        (
            order_id,
            customer_id,
            payment_method,
            payment_status,
            payment_date,
            amount,
            transaction_id
        )
        VALUES
        (
            ?,
            ?,
            ?,
            ?,
            CURDATE(),
            ?,
            ?
        )
    ";


    $paymentStmt = mysqli_prepare(
        $conn,
        $paymentSql
    );


    mysqli_stmt_bind_param(
        $paymentStmt,
        "iissds",
        $order_id,
        $customer_id,
        $payment_method,
        $payment_status,
        $amount,
        $transaction_id
    );


    mysqli_stmt_execute($paymentStmt);


    // ==================================================
    // 12. CLEAR CART
    // ==================================================

    $deleteSql = "
        DELETE FROM cart
        WHERE customer_id = ?
    ";


    $deleteStmt = mysqli_prepare(
        $conn,
        $deleteSql
    );


    mysqli_stmt_bind_param(
        $deleteStmt,
        "i",
        $customer_id
    );


    mysqli_stmt_execute($deleteStmt);


    // ==================================================
    // 13. COMMIT
    // ==================================================

    mysqli_commit($conn);


    $success = true;


}
catch (Throwable $e) {


    // ==================================================
    // ROLLBACK
    // ==================================================

    mysqli_rollback($conn);


    $success = false;

    $error_message = $e->getMessage();

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1"
>

<title>

<?php

echo $success
    ? "Order Successful"
    : "Order Failed";

?>

- Sweet Shop

</title>


<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet"
>


<link
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
rel="stylesheet"
>


<style>

body {

    margin: 0;

    min-height: 100vh;

    display: flex;

    align-items: center;

    justify-content: center;

    background:
    linear-gradient(
        135deg,
        #fff5f5,
        #ffe0e0
    );

    font-family: Arial, sans-serif;

}


.result-card {

    width: 550px;

    max-width: 94%;

    background: white;

    border-radius: 20px;

    padding: 45px 35px;

    text-align: center;

    box-shadow:
    0 15px 40px
    rgba(0,0,0,0.15);

}


.success-icon,
.error-icon {

    width: 90px;

    height: 90px;

    margin: 0 auto 25px;

    border-radius: 50%;

    color: white;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 48px;

}


.success-icon {

    background: #198754;

}


.error-icon {

    background: #dc3545;

}


.success-title {

    color: #198754;

    font-weight: bold;

}


.error-title {

    color: #dc3545;

    font-weight: bold;

}


.order-info {

    margin-top: 25px;

    background: #f8f9fa;

    border-radius: 12px;

    padding: 20px;

    text-align: left;

}


.order-info p {

    margin-bottom: 10px;

}


.order-info strong {

    color: #555;

}


.btn-custom {

    padding: 12px 25px;

    border-radius: 8px;

    font-weight: bold;

}


.logo {

    font-size: 35px;

    margin-bottom: 10px;

}


.shop-name {

    color: #8B0000;

    font-weight: bold;

    font-size: 22px;

    margin-bottom: 25px;

}

</style>

</head>


<body>


<div class="result-card">


<div class="logo">

🍬

</div>


<div class="shop-name">

Sweet Shop

</div>


<?php if ($success) { ?>


<!-- ==========================================
     SUCCESS
========================================== -->


<div class="success-icon">

<i class="bi bi-check-lg"></i>

</div>


<h2 class="success-title">

Order Placed Successfully!

</h2>


<p class="text-muted">

Thank you for shopping with us.

Your order has been received.

</p>


<div class="order-info">


<p>

<strong>Order ID:</strong>

#

<?php

echo $order_id;

?>

</p>


<p>

<strong>Payment Method:</strong>

<?php

echo htmlspecialchars(
    $payment_method
);

?>

</p>


<p>

<strong>Payment Status:</strong>

<span class="badge bg-warning text-dark">

<?php

echo htmlspecialchars(
    $payment_status
);

?>

</span>

</p>


<p>

<strong>Transaction ID:</strong>

<br>

<small>

<?php

echo htmlspecialchars(
    $transaction_id
);

?>

</small>

</p>


<p class="mb-0">

<strong>Total Amount:</strong>

<span class="text-success fw-bold">

₹<?php

echo number_format(
    $grandTotal,
    2
);

?>

</span>

</p>


</div>


<div class="mt-4">


<a
href="my_orders.php"
class="btn btn-primary btn-custom me-2"
>

<i class="bi bi-bag-check"></i>

My Orders

</a>


<a
href="sweets.php"
class="btn btn-warning btn-custom"
>

<i class="bi bi-shop"></i>

Continue Shopping

</a>


</div>


<?php } else { ?>


<!-- ==========================================
     ERROR
========================================== -->


<div class="error-icon">

<i class="bi bi-x-lg"></i>

</div>


<h2 class="error-title">

Order Failed

</h2>


<p class="text-muted">

Sorry, we could not place your order.

</p>


<div class="alert alert-danger text-start">

<strong>Database Error:</strong>

<br><br>

<?php

echo htmlspecialchars(
    $error_message
);

?>

</div>


<div class="mt-4">


<a
href="checkout.php"
class="btn btn-primary btn-custom"
>

<i class="bi bi-arrow-left"></i>

Back to Checkout

</a>


<a
href="cart.php"
class="btn btn-secondary btn-custom"
>

View Cart

</a>


</div>


<?php } ?>


</div>


<script
src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


</body>

</html>