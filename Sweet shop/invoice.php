<?php
session_start();
include("config/db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: my_orders.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$order_id = (int)$_GET['id'];

// Get order details
$orderQuery = mysqli_query($conn,
    "SELECT * FROM orders
     WHERE order_id='$order_id'
     AND user_id='$user_id'");

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
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Invoice #<?php echo $order['order_id']; ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
@media print{
    .no-print{
        display:none;
    }
}
</style>

</head>

<body>

<div class="container my-5">

<div class="card shadow">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<h2 class="text-primary">
Sweet Shop
</h2>

<p>
Online Sweet Shopping & Storage System
</p>

</div>

<div class="text-end">

<h4>
Invoice
</h4>

<p>
Invoice No:
<strong>#<?php echo $order['order_id']; ?></strong>
</p>

<p>
Date:
<?php echo $order['order_date']; ?>
</p>

</div>

</div>

<hr>

<div class="row">

<div class="col-md-6">

<h5>Billing Details</h5>

<p>

<strong>Name:</strong>
<?php echo htmlspecialchars($order['customer_name']); ?>

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

<h5>Order Information</h5>

<p>

<strong>Payment Method:</strong>

<?php echo htmlspecialchars($order['payment_method']); ?>

</p>

<p>

<strong>Payment Status:</strong>

<?php echo htmlspecialchars($order['payment_status']); ?>

</p>

<p>

<strong>Order Status:</strong>

<?php echo htmlspecialchars($order['order_status']); ?>

</p>

</div>

</div>

<hr>

<h5 class="mb-3">Ordered Items</h5>

<div class="table-responsive">

<table class="table table-bordered">

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
src="uploads/sweets/<?php echo $item['image']; ?>"
width="70"
height="70"
style="object-fit:cover;">

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

<div class="text-end">

<h5>

Payment Status:

<span class="badge bg-<?php echo ($order['payment_status']=="Paid") ? "success" : "warning"; ?>">

<?php echo htmlspecialchars($order['payment_status']); ?>

</span>

</h5>

</div>

<div class="mt-4 no-print">

<a href="my_orders.php"
class="btn btn-secondary">

Back to My Orders

</a>

<button
onclick="window.print();"
class="btn btn-primary">

Print Invoice

</button>

</div>

</div>

</div>

</div>

</body>

</html>

