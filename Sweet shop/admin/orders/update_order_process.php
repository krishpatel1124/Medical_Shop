<?php

session_start();
include("../../config/db.php");


// ==========================================
// ADMIN LOGIN
// ==========================================

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}


// ==========================================
// POST CHECK
// ==========================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: view.php");
    exit();
}


// ==========================================
// GET DATA
// ==========================================

$order_id = (int)($_POST['order_id'] ?? 0);

$order_status = mysqli_real_escape_string(
    $conn,
    $_POST['order_status'] ?? 'pending'
);

$payment_status = mysqli_real_escape_string(
    $conn,
    $_POST['payment_status'] ?? 'Pending'
);

$payment_method = mysqli_real_escape_string(
    $conn,
    $_POST['payment_method'] ?? 'COD'
);


if ($order_id <= 0) {
    die("Invalid Order ID.");
}


// ==========================================
// GET CUSTOMER ID FROM ORDER
// ==========================================

$orderQuery = mysqli_query(
    $conn,
    "SELECT customer_id
     FROM `order`
     WHERE order_id = '$order_id'
     LIMIT 1"
);

if (!$orderQuery) {
    die("Order Query Error: " . mysqli_error($conn));
}

if (mysqli_num_rows($orderQuery) == 0) {
    die("Order not found.");
}

$orderData = mysqli_fetch_assoc($orderQuery);

$customer_id = (int)$orderData['customer_id'];


// ==========================================
// UPDATE ORDER
// ==========================================

$updateOrder = mysqli_query(
    $conn,
    "UPDATE `order`
     SET order_status = '$order_status'
     WHERE order_id = '$order_id'"
);

if (!$updateOrder) {
    die("Order Update Error: " . mysqli_error($conn));
}


// ==========================================
// CHECK PAYMENT RECORD
// ==========================================

$paymentQuery = mysqli_query(
    $conn,
    "SELECT payment_id
     FROM payment
     WHERE order_id = '$order_id'
     LIMIT 1"
);

if (!$paymentQuery) {
    die("Payment Query Error: " . mysqli_error($conn));
}


// ==========================================
// PAYMENT EXISTS
// ==========================================

if (mysqli_num_rows($paymentQuery) > 0) {

    $paymentData = mysqli_fetch_assoc($paymentQuery);

    $payment_id = (int)$paymentData['payment_id'];


    $updatePayment = mysqli_query(
        $conn,
        "UPDATE payment
         SET payment_method = '$payment_method',
             payment_status = '$payment_status'
         WHERE payment_id = '$payment_id'"
    );

    if (!$updatePayment) {
        die("Payment Update Error: " . mysqli_error($conn));
    }

}


// ==========================================
// PAYMENT DOES NOT EXIST
// ==========================================

else {

    /*
       IMPORTANT:

       We do NOT insert an empty transaction_id.

       If transaction_id is required and UNIQUE,
       generate a unique value.
    */

    $transaction_id = 'TXN-' .
        $order_id . '-' .
        time();


    $insertPayment = mysqli_query(
        $conn,
        "INSERT INTO payment
        (
            order_id,
            customer_id,
            payment_method,
            payment_status,
            transaction_id
        )
        VALUES
        (
            '$order_id',
            '$customer_id',
            '$payment_method',
            '$payment_status',
            '$transaction_id'
        )"
    );


    if (!$insertPayment) {
        die(
            "Payment Insert Error: " .
            mysqli_error($conn)
        );
    }

}


// ==========================================
// SUCCESS
// ==========================================

header(
    "Location: update_order.php?id=" .
    $order_id .
    "&updated=1"
);

exit();

?>