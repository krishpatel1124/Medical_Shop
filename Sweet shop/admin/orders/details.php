<?php

session_start();
include("../../config/db.php");

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);


// =====================================================
// ADMIN LOGIN CHECK
// =====================================================

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}


// =====================================================
// ORDER ID CHECK
// =====================================================

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: view.php");
    exit();
}

$order_id = (int)$_GET['id'];


// =====================================================
// GET ORDER + CUSTOMER
// =====================================================

$orderSql = "
    SELECT
        o.order_id,
        o.customer_id,
        o.supplier_id,
        o.order_date,
        o.total_amount,
        o.order_status,

        c.name AS customer_name,
        c.email AS customer_email,
        c.mobile_no AS customer_phone,
        c.address AS customer_address

    FROM `order` o

    LEFT JOIN customer_detail c
        ON o.customer_id = c.customer_id

    WHERE o.order_id = ?
";

$stmt = mysqli_prepare($conn, $orderSql);

mysqli_stmt_bind_param($stmt, "i", $order_id);

mysqli_stmt_execute($stmt);

$orderResult = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($orderResult) == 0) {
    die("Order not found.");
}

$order = mysqli_fetch_assoc($orderResult);

mysqli_stmt_close($stmt);


// =====================================================
// GET PAYMENT DETAILS
// =====================================================

$paymentSql = "
    SELECT
        payment_id,
        transaction_id,
        payment_method,
        payment_status,
        payment_date
    FROM payment
    WHERE order_id = ?
    ORDER BY payment_id DESC
    LIMIT 1
";

$stmt = mysqli_prepare($conn, $paymentSql);

mysqli_stmt_bind_param($stmt, "i", $order_id);

mysqli_stmt_execute($stmt);

$paymentResult = mysqli_stmt_get_result($stmt);

$payment = null;

if (mysqli_num_rows($paymentResult) > 0) {
    $payment = mysqli_fetch_assoc($paymentResult);
}

mysqli_stmt_close($stmt);


// =====================================================
// PAYMENT VALUES
// =====================================================

$payment_method = $payment['payment_method'] ?? 'Not Available';

$payment_status = $payment['payment_status'] ?? 'Pending';


// =====================================================
// GET ALL ORDER ITEMS
// =====================================================
//
// IMPORTANT:
// LEFT JOIN is used instead of INNER JOIN.
// Therefore, ALL order_items will be displayed.
// Even if a sweet record is missing.
//

$itemSql = "
    SELECT
        oi.order_item_id,
        oi.order_id,
        oi.sweet_id,
        oi.price,
        oi.quantity,

        s.sweet_name,
        s.image

    FROM order_items oi

    LEFT JOIN sweets s
        ON oi.sweet_id = s.sweet_id

    WHERE oi.order_id = ?

    ORDER BY oi.order_item_id ASC
";

$stmt = mysqli_prepare($conn, $itemSql);

mysqli_stmt_bind_param($stmt, "i", $order_id);

mysqli_stmt_execute($stmt);

$itemResult = mysqli_stmt_get_result($stmt);


// =====================================================
// SUCCESS MESSAGE
// =====================================================

$updated = isset($_GET['updated']) && $_GET['updated'] == 1;

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1">

<title>
Order #<?php echo $order['order_id']; ?>
</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body {
    background: #f5f6fa;
}

.card {
    border: none;
    border-radius: 12px;
}

.card-header {
    border-radius: 12px 12px 0 0 !important;
}

.product-image {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 10px;
    border: 1px solid #ddd;
}

.no-image {
    width: 80px;
    height: 80px;
    background: #eee;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    color: #777;
    text-align: center;
}

.status-badge {
    font-size: 14px;
    padding: 7px 12px;
}

</style>

</head>

<body>


<div class="container py-5">


<!-- =====================================================
     PAGE HEADER
===================================================== -->

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h2 class="fw-bold">
            Order #<?php echo $order['order_id']; ?>
        </h2>

        <p class="text-muted mb-0">
            Order details and management
        </p>

    </div>
<a 
href="../dashboard.php"
   class="btn btn-secondary">

    ← Back to Dashboard

</a>
</div>


<!-- =====================================================
     SUCCESS MESSAGE
===================================================== -->

<?php if ($updated) { ?>

<div class="alert alert-success alert-dismissible fade show">

    <strong>Success!</strong>
    Order status updated successfully.

    <button type="button"
            class="btn-close"
            data-bs-dismiss="alert">
    </button>

</div>

<?php } ?>


<!-- =====================================================
     CUSTOMER + ORDER INFORMATION
===================================================== -->

<div class="row g-4">


<!-- CUSTOMER DETAILS -->

<div class="col-md-6">

<div class="card shadow h-100">

<div class="card-header bg-primary text-white">

<h4 class="mb-0">
    Customer Details
</h4>

</div>


<div class="card-body">

<p>
<strong>Name:</strong><br>

<?php

echo htmlspecialchars(
    $order['customer_name'] ?? 'Not Available'
);

?>

</p>


<p>
<strong>Email:</strong><br>

<?php

echo htmlspecialchars(
    $order['customer_email'] ?? 'Not Available'
);

?>

</p>


<p>
<strong>Phone:</strong><br>

<?php

echo htmlspecialchars(
    $order['customer_phone'] ?? 'Not Available'
);

?>

</p>


<p>

<strong>Address:</strong><br>

<?php

echo nl2br(
    htmlspecialchars(
        $order['customer_address'] ?? 'Not Available'
    )
);

?>

</p>

</div>

</div>

</div>


<!-- ORDER INFORMATION -->

<div class="col-md-6">

<div class="card shadow h-100">

<div class="card-header bg-dark text-white">

<h4 class="mb-0">
    Order Information
</h4>

</div>


<div class="card-body">


<p>

<strong>Order Date:</strong>

<?php

echo htmlspecialchars(
    $order['order_date']
);

?>

</p>


<p>

<strong>Total Amount:</strong>

<span class="text-success fw-bold">

₹<?php

echo number_format(
    $order['total_amount'],
    2
);

?>

</span>

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

<?php

if ($payment_status == "Paid") {

    echo '<span class="badge bg-success status-badge">
            Paid
          </span>';

} elseif ($payment_status == "Pending") {

    echo '<span class="badge bg-warning text-dark status-badge">
            Pending
          </span>';

} else {

    echo '<span class="badge bg-secondary status-badge">'
        . htmlspecialchars($payment_status) .
        '</span>';

}

?>

</p>


<p>

<strong>Order Status:</strong>

<?php

$orderStatus = strtolower(
    $order['order_status']
);

if ($orderStatus == "pending") {

    echo '<span class="badge bg-warning text-dark status-badge">
            Pending
          </span>';

} elseif ($orderStatus == "processing") {

    echo '<span class="badge bg-info status-badge">
            Processing
          </span>';

} elseif ($orderStatus == "delivered") {

    echo '<span class="badge bg-success status-badge">
            Delivered
          </span>';

} elseif ($orderStatus == "cancelled") {

    echo '<span class="badge bg-danger status-badge">
            Cancelled
          </span>';

} else {

    echo '<span class="badge bg-secondary status-badge">'
        . htmlspecialchars($order['order_status']) .
        '</span>';

}

?>

</p>


<?php if ($payment) { ?>

<hr>

<p class="mb-1">
<strong>Transaction ID:</strong>
</p>

<p class="text-muted">

<?php

echo htmlspecialchars(
    $payment['transaction_id'] ?? 'N/A'
);

?>

</p>

<?php } ?>


</div>

</div>

</div>

</div>


<!-- =====================================================
     ORDERED ITEMS
===================================================== -->

<div class="card shadow mt-4">


<div class="card-header bg-success text-white">

<div class="d-flex justify-content-between">

<h4 class="mb-0">
    Ordered Items
</h4>

<span>

<?php

echo mysqli_num_rows($itemResult);

?>

 Items

</span>

</div>

</div>


<div class="card-body">


<div class="table-responsive">

<table class="table table-bordered table-hover align-middle">


<thead class="table-dark">

<tr>

<th width="100">
Image
</th>

<th>
Sweet Name
</th>

<th>
Sweet ID
</th>

<th>
Price
</th>

<th>
Quantity
</th>

<th>
Subtotal
</th>

</tr>

</thead>


<tbody>

<?php

$grandTotal = 0;

if (mysqli_num_rows($itemResult) > 0) {

    while ($item = mysqli_fetch_assoc($itemResult)) {

        $price = (float)$item['price'];

        $quantity = (int)$item['quantity'];

        $subtotal = $price * $quantity;

        $grandTotal += $subtotal;

?>


<tr>


<!-- IMAGE -->

<td>

<?php

if (!empty($item['image'])) {

?>

<img
src="../../uploads/sweets/<?php
echo htmlspecialchars($item['image']);
?>"
class="product-image"
alt="Sweet">

<?php

} else {

?>

<div class="no-image">

    Image<br>
    Not Found

</div>

<?php

}

?>

</td>


<!-- SWEET NAME -->

<td>

<strong>

<?php

if (!empty($item['sweet_name'])) {

    echo htmlspecialchars(
        $item['sweet_name']
    );

} else {

    echo '<span class="text-danger">
            Sweet Deleted / Not Found
          </span>';

}

?>

</strong>

</td>


<!-- SWEET ID -->

<td>

#<?php

echo htmlspecialchars(
    $item['sweet_id']
);

?>

</td>


<!-- PRICE -->

<td>

₹<?php

echo number_format(
    $price,
    2
);

?>

</td>


<!-- QUANTITY -->

<td>

<span class="badge bg-primary">

<?php

echo $quantity;

?>

</span>

</td>


<!-- SUBTOTAL -->

<td>

<strong>

₹<?php

echo number_format(
    $subtotal,
    2
);

?>

</strong>

</td>


</tr>


<?php

    }

} else {

?>

<tr>

<td colspan="6"
    class="text-center text-danger py-4">

<h5>
    No ordered items found.
</h5>

<p class="mb-0">
    No records exist in the order_items table
    for this order.
</p>

</td>

</tr>

<?php

}

?>

</tbody>


<tfoot>

<tr>

<th colspan="5"
    class="text-end">

Calculated Total

</th>

<th>

₹<?php

echo number_format(
    $grandTotal,
    2
);

?>

</th>

</tr>


<tr>

<th colspan="5"
    class="text-end">

Order Total

</th>

<th class="text-success">

₹<?php

echo number_format(
    $order['total_amount'],
    2
);

?>

</th>

</tr>

</tfoot>


</table>

</div>

</div>

</div>


<!-- =====================================================
     UPDATE STATUS
===================================================== -->

<div class="card shadow mt-4">

<div class="card-header bg-warning">

<h4 class="mb-0">
    Update Order
</h4>

</div>


<div class="card-body">


<form
action="update_status.php"
method="POST">


<input
type="hidden"
name="order_id"
value="<?php
echo $order['order_id'];
?>">


<div class="row g-3">


<button
type="submit"
class="btn btn-primary w-100">

Update Order

</button>

</div>


</div>

</form>


</div>

</div>


<!-- =====================================================
     BUTTONS
===================================================== -->

<div class="mt-4 d-flex gap-2">

<a
href="view.php"
class="btn btn-secondary">

← Back to Orders

</a>


<a
href="invoice.php?id=<?php
echo $order['order_id'];
?>"
class="btn btn-success">

🧾 Print Invoice

</a>


</div>


</div>


<script
src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>


</body>

</html>