<?php
session_start();
include("../../config/db.php");

// Check admin login
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// Check order ID
if (!isset($_GET['id'])) {
    header("Location: view.php");
    exit();
}

$order_id = (int)$_GET['id'];

// Get order details
$orderQuery = mysqli_query($conn,
    "SELECT 
        o.*,
        c.name AS customer_name,
        c.email,
        c.mobile_no AS phone,
        c.address
     FROM `order` o
     LEFT JOIN customer_detail c
        ON o.customer_id = c.customer_id
     WHERE o.order_id = '$order_id'"
);
if (mysqli_num_rows($orderQuery) == 0) {
    die("Invoice not found.");
}

$order = mysqli_fetch_assoc($orderQuery);

// Get ordered items
$itemQuery = mysqli_query($conn,
    "SELECT order_items.*,
            sweets.sweet_name,
            sweets.image
     FROM order_items
     INNER JOIN sweets
     ON order_items.sweet_id = sweets.sweet_id
     WHERE order_items.order_id='$order_id'");

     // ==========================================
// GET PAYMENT DETAILS
// ==========================================

$paymentQuery = mysqli_query(
    $conn,
    "SELECT
        payment_method,
        payment_status,
        transaction_id
     FROM payment
     WHERE order_id = '$order_id'
     LIMIT 1"
);

if (!$paymentQuery) {
    die("Payment SQL Error: " . mysqli_error($conn));
}

$payment = mysqli_fetch_assoc($paymentQuery);

// If payment record does not exist
if (!$payment) {
    $payment = [
        'payment_method' => 'Not Available',
        'payment_status' => 'Pending',
        'transaction_id' => 'Not Available'
    ];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Invoice #<?php echo $order['order_id']; ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    background:#f8f9fa;
}

.invoice-box{
    background:#fff;
    padding:30px;
    border-radius:10px;
}

@media print{

.no-print{
display:none;
}

body{
background:white;
}

}

</style>

</head>

<body>

<div class="container my-5">

<div class="invoice-box shadow">

<div class="d-flex justify-content-between align-items-center">

<div>

<h2 class="text-primary">
krishna sweets
</h2>

<p class="mb-0">
Online Sweet Shopping & Storage System
</p>

</div>

<div class="text-end">

<h3>
INVOICE
</h3>

<p class="mb-0">
Invoice No:
<strong>#<?php echo $order['order_id']; ?></strong>
</p>

<p>
<?php echo $order['order_date']; ?>
</p>

</div>

</div>

<hr>

<div class="row">

<div class="col-md-6">

<h5>Customer Details</h5>

<p>
    <strong>Name:</strong>
    <?php
    echo htmlspecialchars(
        $order['customer_name'] ?? 'N/A'
    );
    ?>
</p>

<p>
<strong>Email:</strong>
<?php echo htmlspecialchars($order['email']); ?>
</p>

<p>
<strong>Phone:</strong>
<?php echo htmlspecialchars($order['phone']); ?>
</p>

<p>
<strong>Address:</strong><br>

<?php echo nl2br(htmlspecialchars($order['address'])); ?>

</p>

</div>

<div class="col-md-6">
</div>

</div>

<hr>

<h5 class="mb-3">Ordered Items</h5>

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead class="table-dark">

<tr>

<th>Image</th>
<th>Sweet Name</th>
<th>Price</th>
<th>Quantity</th>
<th>Subtotal</th>

</tr>

</thead>

<tbody>

<?php

while($item = mysqli_fetch_assoc($itemQuery))
{

    $subtotal = $item['price'] * $item['quantity'];

?>

<tr>

<td>

<img
src="../../uploads/sweets/<?php echo htmlspecialchars($item['image']); ?>"
width="70"
height="70"
style="object-fit:cover;border-radius:8px;">

</td>

<td>

<?php echo htmlspecialchars($item['sweet_name']); ?>

</td>

<td>

₹<?php echo number_format($item['price'],2); ?>

</td>

<td>

<?php echo $item['quantity']; ?>

</td>

<td>

₹<?php echo number_format($subtotal,2); ?>

</td>

</tr>

<?php

}

?>

</tbody>

<tfoot>

<tr>

<th colspan="4" class="text-end">

Grand Total

</th>

<th>

₹<?php echo number_format($order['total_amount'],2); ?>

</th>

</tr>

</tfoot>

</table>

</div>

<hr>

<div class="row">

<div class="col-md-6">

<!-- ==========================================
     PAYMENT SUMMARY
========================================== -->

<div class="payment-summary">

    <h5>Payment Summary</h5>

    <p>
        <strong>Payment Method:</strong>
        <?php
        echo htmlspecialchars(
            $payment['payment_method'] ?? 'Not Available'
        );
        ?>
    </p>

    <p>
        <strong>Payment Status:</strong>
        <?php
        echo htmlspecialchars(
            $payment['payment_status'] ?? 'Pending'
        );
    ?>
    </p>

</div>

</span>

</p>

</div>

<div class="col-md-6 text-end">

<h4>

Total Payable

</h4>

<h2 class="text-success">

₹<?php echo number_format($order['total_amount'],2); ?>

</h2>

</div>

</div>

<hr>

<div class="text-center mt-4">

<p class="text-muted">

Thank you for choosing <strong>krishna sweets</strong>.

</p>

<p class="text-muted">

This is a computer-generated invoice and does not require a signature.

</p>

</div>

<hr>

<div class="d-flex justify-content-between no-print">

<a href="view.php" class="btn btn-secondary">

← Back to Orders

</a>

<button
onclick="window.print();"
class="btn btn-primary">

🖨 Print Invoice

</button>

</div>

</div>

</div>

</body>

</html>

