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
// CHECK ORDER ID
// ==========================================

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: view.php");
    exit();
}

$order_id = (int) $_GET['id'];


// ==========================================
// GET ORDER DETAILS
// ==========================================

$orderQuery = mysqli_query(
    $conn,
    "SELECT
        o.*,
        c.name AS customer_name,
        c.email,
        c.mobile_no AS phone,
        c.address
     FROM `order` o
     LEFT JOIN customer_detail c
        ON o.customer_id = c.customer_id
     WHERE o.order_id = '$order_id'
     LIMIT 1"
);

if (!$orderQuery) {
    die("Order Query Error: " . mysqli_error($conn));
}

if (mysqli_num_rows($orderQuery) == 0) {
    die("Order not found.");
}

$order = mysqli_fetch_assoc($orderQuery);


// ==========================================
// GET PAYMENT DETAILS
// ==========================================

$paymentQuery = mysqli_query(
    $conn,
    "SELECT *
     FROM payment
     WHERE order_id = '$order_id'
     LIMIT 1"
);

if (!$paymentQuery) {
    die("Payment Query Error: " . mysqli_error($conn));
}

$payment = mysqli_fetch_assoc($paymentQuery);


// ==========================================
// PAYMENT VALUES
// ==========================================

$payment_method = $payment['payment_method'] ?? 'COD';
$payment_status = $payment['payment_status'] ?? 'Pending';


// ==========================================
// SUCCESS MESSAGE
// ==========================================

$success = "";

if (isset($_GET['updated'])) {
    $success = "Order updated successfully.";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1">

<title>Update Order #<?php echo $order_id; ?></title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">


<style>

body {
    background: #f5f5f5;
}

.navbar {
    background: #8B0000;
}

.navbar-brand {
    color: white;
    font-size: 25px;
    font-weight: bold;
}

.navbar-brand:hover {
    color: #FFD700;
}

.card {
    border: none;
    border-radius: 12px;
}

.order-title {
    color: #8B0000;
    font-weight: bold;
}

.info-label {
    font-weight: bold;
    color: #555;
}

</style>

</head>


<body>


<!-- ==========================================
     NAVBAR
========================================== -->

<nav class="navbar mb-4">

<div class="container">

<a
href="../dashboard.php"
class="navbar-brand">

🍬 Sweet Shop

</a>
<a href="/new%20folder/sweet%20shop/Sweet%20shop/admin/dashboard.php"
   class="btn btn-secondary">

    ← Back to Dashboard

</a>
<a
href="details.php?id=<?php echo $order_id; ?>"
class="btn btn-info text-white">

<i class="bi bi-eye"></i>

View Order

</a>


<a
href="invoice.php?id=<?php echo $order_id; ?>"
class="btn btn-success">

<i class="bi bi-printer"></i>

Print Invoice

</a>



</div>

</nav>


<!-- ==========================================
     MAIN
========================================== -->

<div class="container pb-5">


<?php if ($success != "") { ?>

<div class="alert alert-success alert-dismissible fade show">

<i class="bi bi-check-circle-fill"></i>

<?php echo $success; ?>

<button
type="button"
class="btn-close"
data-bs-dismiss="alert">
</button>

</div>

<?php } ?>


<!-- ==========================================
     TITLE
========================================== -->

<div class="text-center mb-4">

<h2 class="order-title">

<i class="bi bi-pencil-square"></i>

Update Order #<?php echo $order_id; ?>

</h2>

<p class="text-muted">

Update order and payment information

</p>

</div>


<div class="row g-4">


<!-- ==========================================
     CUSTOMER DETAILS
========================================== -->

<div class="col-md-6">

<div class="card shadow h-100">

<div class="card-header bg-primary text-white">

<h5 class="mb-0">

<i class="bi bi-person"></i>

Customer Details

</h5>

</div>


<div class="card-body">

<p>

<span class="info-label">
Name:
</span>

<?php
echo htmlspecialchars(
    $order['customer_name'] ?? 'Not Available'
);
?>

</p>


<p>

<span class="info-label">
Email:
</span>

<?php
echo htmlspecialchars(
    $order['email'] ?? 'Not Available'
);
?>

</p>


<p>

<span class="info-label">
Phone:
</span>

<?php
echo htmlspecialchars(
    $order['phone'] ?? 'Not Available'
);
?>

</p>


<p>

<span class="info-label">
Address:
</span>

<br>

<?php

echo nl2br(
    htmlspecialchars(
        $order['address'] ?? 'Not Available'
    )
);

?>

</p>

</div>

</div>

</div>


<!-- ==========================================
     ORDER INFORMATION
========================================== -->

<div class="col-md-6">

<div class="card shadow h-100">

<div class="card-header bg-success text-white">

<h5 class="mb-0">

<i class="bi bi-receipt"></i>

Order Information

</h5>

</div>


<div class="card-body">

<p>

<span class="info-label">
Order ID:
</span>

#<?php echo $order['order_id']; ?>

</p>


<p>

<span class="info-label">
Order Date:
</span>

<?php echo $order['order_date']; ?>

</p>


<p>

<span class="info-label">
Total Amount:
</span>

<strong class="text-success">

₹<?php
echo number_format(
    $order['total_amount'],
    2
);
?>

</strong>

</p>


<p>

<span class="info-label">
Payment Method:
</span>

<?php
echo htmlspecialchars($payment_method);
?>

</p>

</div>

</div>

</div>


</div>


<!-- ==========================================
     UPDATE FORM
========================================== -->

<div class="card shadow mt-4">

<div class="card-header bg-warning">

<h4 class="mb-0">

<i class="bi bi-arrow-repeat"></i>

Update Order Status

</h4>

</div>


<div class="card-body">


<form
action="update_order_process.php"
method="POST">


<input
type="hidden"
name="order_id"
value="<?php echo $order_id; ?>">


<div class="row g-4">


<!-- ORDER STATUS -->

<div class="col-md-6">

<label class="form-label fw-bold">

Order Status

</label>


<select
name="order_status"
class="form-select"
required>


<option
value="pending"
<?php
if (
    strtolower($order['order_status']) == 'pending'
) {
    echo 'selected';
}
?>>

Pending

</option>


<option
value="processing"
<?php
if (
    strtolower($order['order_status']) == 'processing'
) {
    echo 'selected';
}
?>>

Processing

</option>


<option
value="delivered"
<?php
if (
    strtolower($order['order_status']) == 'delivered'
) {
    echo 'selected';
}
?>>

Delivered

</option>


<option
value="cancelled"
<?php
if (
    strtolower($order['order_status']) == 'cancelled'
) {
    echo 'selected';
}
?>>

Cancelled

</option>


</select>

</div>


<!-- PAYMENT STATUS -->

<div class="col-md-6">

<label class="form-label fw-bold">

Payment Status

</label>


<select
name="payment_status"
class="form-select"
required>


<option
value="Pending"
<?php
if (
    strtolower($payment_status) == 'pending'
) {
    echo 'selected';
}
?>>

Pending

</option>


<option
value="Paid"
<?php
if (
    strtolower($payment_status) == 'paid'
) {
    echo 'selected';
}
?>>

Paid

</option>


</select>

</div>


<!-- PAYMENT METHOD -->

<div class="col-md-6">

<label class="form-label fw-bold">

Payment Method

</label>


<select
name="payment_method"
class="form-select">


<option
value="COD"
<?php
if (
    strtolower($payment_method) == 'cod'
) {
    echo 'selected';
}
?>>

COD

</option>


<option
value="UPI"
<?php
if (
    strtolower($payment_method) == 'UPI'
) {
    echo 'selected';
}
?>>

UPI

</option>

<option
value="DEBIT CARD"
<?php
if (
    strtolower($payment_method) == 'DEBIT CARD'
) {
    echo 'selected';
}
?>>
DEBIT CARD


</option>

<option
value="CREDIT CARD"
<?php
if (
    strtolower($payment_method) == 'CREDIT CARD'
) {
    echo 'selected';
}
?>>

CREDIT CARD

</option>



</select>

</div>


<!-- UPDATE BUTTON -->

<div class="col-md-6 d-flex align-items-end">

<button
type="submit"
class="btn btn-primary btn-lg w-100">

<i class="bi bi-check-circle"></i>

Update Order

</button>

</div>


</div>

</form>


</div>

</div>

</div>

<script
src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

</body>

</html>