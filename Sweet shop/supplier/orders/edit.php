<?php

session_start();
include("../../config/db.php");

// Check supplier login
if (!isset($_SESSION['supplier_id'])) {
    header("Location: ../../login.php");
    exit();
}

$supplier_id = (int)$_SESSION['supplier_id'];

// Check order ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid Order ID.");
}

$order_id = (int)$_GET['id'];


// Fetch order
$sql = "
SELECT
    o.order_id,
    o.order_status,
    o.total_amount,
    o.order_date,
    c.name
FROM `order` o
INNER JOIN customer_detail c
    ON o.customer_id = c.customer_id
WHERE o.order_id = $order_id
AND o.supplier_id = $supplier_id
";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Database Error: " . mysqli_error($conn));
}

if (mysqli_num_rows($result) == 0) {
    die("Order not found.");
}

$order = mysqli_fetch_assoc($result);


// Update order
if (isset($_POST['update_order'])) {

    $order_status = mysqli_real_escape_string(
        $conn,
        $_POST['order_status']
    );

    $update_sql = "
    UPDATE `order`
    SET order_status = '$order_status'
    WHERE order_id = $order_id
    AND supplier_id = $supplier_id
    ";

    if (mysqli_query($conn, $update_sql)) {

        header("Location: view.php?updated=1");
        exit();

    } else {

        $error = mysqli_error($conn);
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Edit Order</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet"
>

<style>

body {
    background: #f5f5f5;
}

.edit-card {
    max-width: 600px;
    margin: 60px auto;
    background: white;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,.1);
}

</style>

</head>

<body>

<div class="container">

<div class="edit-card">

<h2 class="mb-4">
    ✏ Edit Order
</h2>


<?php if (isset($error)) { ?>

<div class="alert alert-danger">
    <?php echo htmlspecialchars($error); ?>
</div>

<?php } ?>


<div class="mb-3">

<label class="form-label">
    Order ID
</label>

<input
type="text"
class="form-control"
value="#<?php echo $order['order_id']; ?>"
readonly
>

</div>


<div class="mb-3">

<label class="form-label">
    Customer
</label>

<input
type="text"
class="form-control"
value="<?php echo htmlspecialchars($order['name']); ?>"
readonly
>

</div>


<div class="mb-3">

<label class="form-label">
    Order Date
</label>

<input
type="text"
class="form-control"
value="<?php echo htmlspecialchars($order['order_date']); ?>"
readonly
>

</div>


<div class="mb-3">

<label class="form-label">
    Total Amount
</label>

<input
type="text"
class="form-control"
value="₹<?php echo number_format($order['total_amount'], 2); ?>"
readonly
>

</div>


<form method="POST">

<div class="mb-4">

<label class="form-label">
    Order Status
</label>

<select
name="order_status"
class="form-select"
required
>

<option
value="Pending"
<?php
if (strtolower($order['order_status']) == 'pending')
    echo 'selected';
?>
>
Pending
</option>

<option
value="Processing"
<?php
if (strtolower($order['order_status']) == 'processing')
    echo 'selected';
?>
>
Processing
</option>

<option
value="Delivered"
<?php
if (strtolower($order['order_status']) == 'delivered')
    echo 'selected';
?>
>
Delivered
</option>

<option
value="Cancelled"
<?php
if (strtolower($order['order_status']) == 'cancelled')
    echo 'selected';
?>
>
Cancelled
</option>

</select>

</div>


<div class="d-flex gap-2">

<button
type="submit"
name="update_order"
class="btn btn-success"
>
    ✓ Update Order
</button>

<a
href="view.php"
class="btn btn-secondary"
>
    ← Back
</a>

</div>

</form>

</div>

</div>

</body>

</html>