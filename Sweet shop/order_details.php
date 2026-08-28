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

// Verify that the order belongs to the logged-in user
$orderQuery = mysqli_query($conn,
    "SELECT * FROM orders
     WHERE order_id='$order_id'
     AND user_id='$user_id'");

if (mysqli_num_rows($orderQuery) == 0) {
    die("Order not found.");
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

<title>Order Details</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container py-5">

<h2 class="mb-4">
Order Details
</h2>

<div class="card shadow mb-4">

<div class="card-header bg-primary text-white">

<h5 class="mb-0">
Order #<?php echo $order['order_id']; ?>
</h5>

</div>

<div class="card-body">

<div class="row">

<div class="col-md-6">

<p><strong>Order Date:</strong> <?php echo $order['order_date']; ?></p>

<p><strong>Payment Method:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>

<p><strong>Payment Status:</strong> <?php echo htmlspecialchars($order['payment_status']); ?></p>

</div>

<div class="col-md-6">

<p><strong>Order Status:</strong> <?php echo htmlspecialchars($order['order_status']); ?></p>

<p><strong>Total Amount:</strong> ₹<?php echo number_format($order['total_amount'],2); ?></p>

</div>

</div>

</div>

</div>

<div class="card shadow">

<div class="card-header bg-success text-white">

<h5 class="mb-0">
Ordered Items
</h5>

</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead class="table-dark">

<tr>

<th>Image</th>
<th>Sweet</th>
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

<img src="uploads/sweets/<?php echo $item['image']; ?>"
     width="70"
     height="70"
     style="object-fit:cover;">

</td>

<td><?php echo htmlspecialchars($item['sweet_name']); ?></td>

<td>₹<?php echo number_format($item['price'],2); ?></td>

<td><?php echo $item['quantity']; ?></td>

<td>₹<?php echo number_format($subtotal,2); ?></td>

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

<div class="mt-3">

<a href="my_orders.php" class="btn btn-secondary">
Back to My Orders
</a>

<a href="invoice.php?id=<?php echo $order['order_id']; ?>" class="btn btn-success">
Download Invoice
</a>

</div>

</div>

</div>

</div>

</body>
</html>

