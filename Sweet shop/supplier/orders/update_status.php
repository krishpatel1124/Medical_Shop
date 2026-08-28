<?php
session_start();
include("../../config/db.php");

if (!isset($_SESSION['supplier_id'])) {
    header("Location:../login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location:view.php");
    exit();
}

$order_id = intval($_GET['id']);

// Update Status
if (isset($_POST['update'])) {

    $status = mysqli_real_escape_string($conn, $_POST['order_status']);

    mysqli_query($conn, "
        UPDATE `order`
        SET order_status='$status'
        WHERE order_id='$order_id'
    ");

    header("Location:view.php?msg=updated");
    exit();
}

// Fetch Order
$result = mysqli_query($conn, "
    SELECT *
    FROM `order`
    WHERE order_id='$order_id'
");

if (mysqli_num_rows($result) == 0) {
    die("Order not found.");
}

$order = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">

<title>Update Order Status</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container mt-5">

<div class="card shadow">

<div class="card-header bg-primary text-white">

<h4>Update Order Status</h4>

</div>

<div class="card-body">

<form method="POST">

<div class="mb-3">

<label class="form-label">Order ID</label>

<input
type="text"
class="form-control"
value="<?php echo $order['order_id']; ?>"
readonly>

</div>

<div class="mb-3">

<label class="form-label">Current Status</label>

<select
name="order_status"
class="form-select"
required>

<option value="Pending"
<?php if($order['order_status']=="Pending") echo "selected"; ?>>
Pending
</option>

<option value="Processing"
<?php if($order['order_status']=="Processing") echo "selected"; ?>>
Processing
</option>

<option value="Delivered"
<?php if($order['order_status']=="Delivered") echo "selected"; ?>>
Delivered
</option>

<option value="Cancelled"
<?php if($order['order_status']=="Cancelled") echo "selected"; ?>>
Cancelled
</option>

</select>

</div>

<button
type="submit"
name="update"
class="btn btn-success">

Update Status

</button>

<a href="view.php" class="btn btn-secondary">

Back

</a>

</form>

</div>

</div>

</div>

</body>
</html>