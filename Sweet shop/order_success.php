<?php
session_start();
include("config/db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['order_id'])) {
    header("Location: index.php");
    exit();
}

$order_id = (int)$_GET['order_id'];
$user_id = $_SESSION['user_id'];

// Get order details
$sql = "SELECT *
        FROM orders
        WHERE order_id='$order_id'
        AND user_id='$user_id'";

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    die("Order not found.");
}

$order = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Order Successful</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container py-5">

<div class="row justify-content-center">

<div class="col-lg-8">

<div class="card shadow">

<div class="card-body text-center">

<h1 class="text-success">🎉 Order Placed Successfully!</h1>

<p class="lead">
Thank you for shopping with us.
</p>

<hr>

<table class="table table-bordered">

<tr>
<th>Order ID</th>
<td>#<?php echo $order['order_id']; ?></td>
</tr>

<tr>
<th>Total Amount</th>
<td>₹<?php echo number_format($order['total_amount'], 2); ?></td>
</tr>

<tr>
<th>Payment Method</th>
<td><?php echo htmlspecialchars($order['payment_method']); ?></td>
</tr>

<tr>
<th>Payment Status</th>
<td>
<span class="badge bg-<?php echo ($order['payment_status']=="Paid") ? "success" : "warning"; ?>">
<?php echo htmlspecialchars($order['payment_status']); ?>
</span>
</td>
</tr>

<tr>
<th>Order Status</th>
<td>
<span class="badge bg-info">
<?php echo htmlspecialchars($order['order_status']); ?>
</span>
</td>
</tr>

<tr>
<th>Order Date</th>
<td><?php echo $order['order_date']; ?></td>
</tr>

</table>

<div class="mt-4">

<a href="index.php" class="btn btn-primary">
Continue Shopping
</a>

<a href="my_orders.php" class="btn btn-success">
My Orders
</a>

</div>

</div>

</div>

</div>

</div>

</div>

</body>
</html>

