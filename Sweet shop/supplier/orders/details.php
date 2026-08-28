<?php

session_start();

include("../../config/db.php");


// ==========================================
// SUPPLIER LOGIN CHECK
// ==========================================

if (!isset($_SESSION['supplier_id'])) {

    header("Location: ../../login.php");
    exit();

}

$supplier_id = (int)$_SESSION['supplier_id'];


// ==========================================
// CHECK ORDER ID
// ==========================================

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    die("Invalid Order ID.");

}

$order_id = (int)$_GET['id'];


// ==========================================
// GET ORDER + CUSTOMER + PAYMENT
// ==========================================

$sql = "

SELECT

    o.order_id,
    o.customer_id,
    o.supplier_id,
    o.order_date,
    o.total_amount,
    o.order_status,

    c.name,
    c.email,
    c.mobile_no,
    c.address,

    p.payment_method,
    p.payment_status

FROM `order` o

INNER JOIN customer_detail c
    ON o.customer_id = c.customer_id

LEFT JOIN payment p
    ON o.order_id = p.order_id

WHERE o.order_id = '$order_id'

AND o.supplier_id = '$supplier_id'

ORDER BY p.payment_id DESC

LIMIT 1

";


$result = mysqli_query($conn, $sql);


if (!$result) {

    die("Database Error: " . mysqli_error($conn));

}


if (mysqli_num_rows($result) == 0) {

    die("Order not found or you are not authorized to view this order.");

}


$order = mysqli_fetch_assoc($result);


// ==========================================
// PAYMENT STATUS
// ==========================================

// If payment method is COD → Pending
// Otherwise → Paid

$payment_method = $order['payment_method'] ?? 'Cash on Delivery';

if (strtolower(trim($payment_method)) == 'cash on delivery') {

    $payment_status = "Pending";

} else {

    $payment_status = "Paid";

}


// ==========================================
// GET ORDERED SWEETS
// ==========================================

$item_sql = "

SELECT

    oi.*,

    s.sweet_name,
    s.image

FROM order_items oi

INNER JOIN sweets s

    ON oi.sweet_id = s.sweet_id

WHERE oi.order_id = '$order_id'

";


$item_result = mysqli_query($conn, $item_sql);


if (!$item_result) {

    die("Order Item Error: " . mysqli_error($conn));

}

?>


<!DOCTYPE html>

<html lang="en">


<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">


<title>

Order #<?php echo $order['order_id']; ?> - Supplier

</title>


<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">


<style>

body {

    background:#f5f6fa;

}


.details-card {

    background:white;

    border-radius:12px;

    padding:25px;

    margin-top:30px;

    margin-bottom:30px;

    box-shadow:0 4px 15px rgba(0,0,0,0.10);

}


.order-title {

    font-weight:bold;

}


.product-image {

    width:60px;

    height:60px;

    object-fit:cover;

    border-radius:8px;

}


.info-box {

    background:#f8f9fa;

    border-radius:8px;

    padding:15px;

    height:100%;

}

</style>

</head>


<body>


<div class="container">


<div class="details-card">


<!-- ==========================================
     HEADER
========================================== -->


<div class="d-flex justify-content-between align-items-center mb-4">


<h2 class="order-title">

Order #<?php echo $order['order_id']; ?>

</h2>


</div>



<!-- ==========================================
     CUSTOMER DETAILS
========================================== -->


<h4 class="mb-3">

Customer Details

</h4>


<div class="row mb-4">


<!-- NAME -->

<div class="col-md-4 mb-3">

<div class="info-box">


<strong>Name</strong>


<br>


<?php

echo htmlspecialchars(
    $order['name']
);

?>


</div>

</div>



<!-- EMAIL -->

<div class="col-md-4 mb-3">

<div class="info-box">


<strong>Email</strong>


<br>


<?php

echo htmlspecialchars(
    $order['email']
);

?>


</div>

</div>



<!-- MOBILE -->

<div class="col-md-4 mb-3">

<div class="info-box">


<strong>Mobile</strong>


<br>


<?php

echo htmlspecialchars(
    $order['mobile_no']
);

?>


</div>

</div>


</div>



<!-- ADDRESS -->

<div class="row mb-4">


<div class="col-md-12">


<div class="info-box">


<strong>Address</strong>


<br>


<?php

echo nl2br(
    htmlspecialchars(
        $order['address'] ?? 'Not Available'
    )
);

?>


</div>


</div>


</div>



<!-- ==========================================
     ORDER INFORMATION
========================================== -->


<h4 class="mb-3">

Order Information

</h4>


<div class="row mb-4">


<!-- ORDER ID -->

<div class="col-md-3 mb-3">

<strong>

Order ID

</strong>


<p>

#<?php echo $order['order_id']; ?>

</p>

</div>



<!-- DATE -->

<div class="col-md-3 mb-3">

<strong>

Order Date

</strong>


<p>

<?php

echo htmlspecialchars(
    $order['order_date']
);

?>

</p>

</div>



<!-- PAYMENT METHOD -->

<div class="col-md-3 mb-3">

<strong>

Payment

</strong>


<p>

<?php

echo htmlspecialchars(
    $payment_method
);

?>

</p>

</div>



<!-- PAYMENT STATUS -->

<div class="col-md-3 mb-3">

<strong>

Payment Status

</strong>


<p>


<?php


if ($payment_status == "Paid") {


    echo '<span class="badge bg-success">

            Paid

          </span>';


} else {


    echo '<span class="badge bg-warning text-dark">

            Pending

          </span>';


}


?>


</p>

</div>


</div>



<!-- ==========================================
     ORDER STATUS
========================================== -->


<div class="mb-4">


<strong>

Order Status:

</strong>


<?php


$status = strtolower(
    trim(
        $order['order_status']
    )
);


if ($status == "pending") {


    echo '

    <span class="badge bg-warning text-dark ms-2">

        Pending

    </span>

    ';


}

elseif ($status == "processing") {


    echo '

    <span class="badge bg-info text-dark ms-2">

        Processing

    </span>

    ';


}

elseif ($status == "delivered") {


    echo '

    <span class="badge bg-success ms-2">

        Delivered

    </span>

    ';


}

elseif ($status == "cancelled") {


    echo '

    <span class="badge bg-danger ms-2">

        Cancelled

    </span>

    ';


}

else {


    echo '

    <span class="badge bg-secondary ms-2">

        '
        . htmlspecialchars(
            $order['order_status']
        )
        . '

    </span>

    ';

}


?>


</div>



<!-- ==========================================
     ORDERED SWEETS
========================================== -->


<h4 class="mb-3">

Ordered Sweets

</h4>


<div class="table-responsive">


<table class="table table-bordered align-middle">


<thead class="table-dark">


<tr>

<th>

Sweet

</th>


<th>

Quantity

</th>


<th>

Price

</th>


<th>

Subtotal

</th>


</tr>


</thead>


<tbody>


<?php


$grandTotal = 0;


if (mysqli_num_rows($item_result) > 0) {


    while (
        $item =
        mysqli_fetch_assoc($item_result)
    ) {


        $quantity =
            (int)$item['quantity'];


        $price =
            (float)$item['price'];


        $subtotal =
            $quantity * $price;


        $grandTotal +=
            $subtotal;


?>


<tr>


<!-- SWEET -->

<td>


<?php

if (!empty($item['image'])) {

?>


<img

src="../../uploads/sweets/<?php
echo htmlspecialchars(
    $item['image']
);
?>"

class="product-image me-2"

alt="Sweet"


>


<?php

}

?>


<strong>

<?php

echo htmlspecialchars(
    $item['sweet_name']
);

?>

</strong>


</td>



<!-- QUANTITY -->

<td>

<?php

echo $quantity;

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



<!-- SUBTOTAL -->

<td>

₹<?php

echo number_format(
    $subtotal,
    2
);

?>

</td>


</tr>


<?php


    }


}

else {


?>


<tr>


<td colspan="4"
    class="text-center text-danger">

No order items found.

</td>


</tr>


<?php

}


?>


</tbody>


<tfoot>


<tr>


<th colspan="3"
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


<th colspan="3"
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



<!-- ==========================================
     BACK BUTTON
========================================== -->


<div class="mt-4">


<a href="view.php"
   class="btn btn-secondary">

← Back to Orders

</a>


<a href="../dashboard.php"
   class="btn btn-success">

← Dashboard

</a>


</div>


</div>


</div>


</body>

</html>