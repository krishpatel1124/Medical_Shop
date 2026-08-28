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
// PAGINATION
// ==========================================

$limit = 10;

$page = isset($_GET['page'])
    ? (int)$_GET['page']
    : 1;

if ($page < 1) {
    $page = 1;
}

$start = ($page - 1) * $limit;



// ==========================================
// ORDER STATUS FILTER
// ==========================================

$status = isset($_GET['status'])
    ? trim($_GET['status'])
    : "";

$status = mysqli_real_escape_string($conn, $status);


// ==========================================
// WHERE CONDITION
// ==========================================

$where = " WHERE 1=1 ";
?>

<section class="container py-5">
<?php
$search = $_GET['search'] ?? '';

$search = mysqli_real_escape_string($conn, $search);

$query = mysqli_query($conn, "
    SELECT *
    FROM sweets
    WHERE sweet_name LIKE '%$search%'
    ORDER BY sweet_name ASC
");
?>
<div class="row justify-content-center">


<div class="col-md-8">


<form
action="sweets.php"
method="GET">


<div class="input-group">


<input
type="text"
name="search"
class="form-control form-control-lg"
placeholder="Search your favourite sweets..."
value="<?php echo ($search); ?>">


<button
type="submit"
class="btn btn-danger btn-lg">

Search

</button>


</div>


</form>


</div>


</div>


</section>

<?php
// Status filter

if ($status != "") {

    $where .= "
        AND LOWER(o.order_status) = LOWER('$status')
    ";

}


// ==========================================
// COUNT TOTAL RECORDS
// ==========================================

$countSql = "
    SELECT COUNT(DISTINCT o.order_id) AS total

    FROM `order` o

    INNER JOIN customer_detail c
        ON o.customer_id = c.customer_id

    $where
";


$countResult = mysqli_query($conn, $countSql);


if (!$countResult) {
    die(
        "Count Query Error: " .
        mysqli_error($conn)
    );
}


$totalRows = mysqli_fetch_assoc($countResult)['total'];

$totalPages = ceil($totalRows / $limit);


// ==========================================
// MAIN QUERY
// ==========================================

$sql = "

SELECT

    o.order_id,

    c.name AS customer_name,

    c.email,

    o.order_date,

    o.total_amount,

    o.order_status,

    p.payment_method,

    p.payment_status

FROM `order` o


INNER JOIN customer_detail c

    ON o.customer_id = c.customer_id


LEFT JOIN payment p

    ON o.order_id = p.order_id


$where


GROUP BY o.order_id


ORDER BY o.order_id DESC


LIMIT $start, $limit

";


$result = mysqli_query($conn, $sql);


if (!$result) {

    die(
        "Order Query Error: " .
        mysqli_error($conn)
    );

}

?>


<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1">


<title>Manage Orders</title>


<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">


<link
rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">


<style>

body {

    background:#f5f5f5;

}


.navbar {

    background:#8B0000;

}


.navbar-brand {

    color:white;

    font-size:26px;

    font-weight:bold;

}


.navbar-brand:hover {

    color:#FFD700;

}


.card {

    border:none;

    border-radius:12px;

}


.badge {

    font-size:13px;

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


<a
href="../dashboard.php"
class="btn btn-light">

<i class="bi bi-speedometer2"></i>

Dashboard

</a>

</div>

</nav>


<div class="container py-4">


<!-- ==========================================
     HEADER
========================================== -->

<div
class="d-flex justify-content-between align-items-center mb-4">


<h2>

<i class="bi bi-cart-check"></i>

Customer Orders

</h2>




</div>


<!-- ==========================================
     CARD
========================================== -->

<div class="card shadow">


<div class="card-header bg-primary text-white">

<h4 class="mb-0">

<i class="bi bi-list-check"></i>

All Orders

</h4>

</div>


<div class="card-body">


<!-- ==========================================
     SEARCH
========================================== -->

<form
method="GET"
class="row g-3 mb-4">


<div class="col-md-4">

<input
type="text"
name="search"
class="form-control"
placeholder="Search Order ID, Customer or Email"
value="<?php echo htmlspecialchars($search); ?>">

</div>


<div class="col-md-3">

<select
name="status"
class="form-select">


<option value="">

All Status

</option>


<option
value="Pending"
<?php

if (
    strtolower($status) == "pending"
) {

    echo "selected";

}

?>>

Pending

</option>


<option
value="Processing"
<?php

if (
    strtolower($status) == "processing"
) {

    echo "selected";

}

?>>

Processing

</option>


<option
value="Delivered"
<?php

if (
    strtolower($status) == "delivered"
) {

    echo "selected";

}

?>>

Delivered

</option>


<option
value="Cancelled"
<?php

if (
    strtolower($status) == "cancelled"
) {

    echo "selected";

}

?>>

Cancelled

</option>


</select>

</div>


<div class="col-md-2">

<button
type="submit"
class="btn btn-primary w-100">

<i class="bi bi-search"></i>

Search

</button>

</div>


<div class="col-md-2">

<a
href="view.php"
class="btn btn-secondary w-100">

<i class="bi bi-arrow-clockwise"></i>

Reset

</a>

</div>


</form>


<!-- ==========================================
     TABLE
========================================== -->

<div class="table-responsive">


<table
class="table table-bordered table-hover align-middle">


<thead class="table-dark">


<tr>

<th>Order ID</th>

<th>Customer</th>

<th>Email</th>

<th>Total</th>

<th>Payment</th>

<th>Payment Status</th>

<th>Order Status</th>

<th>Date</th>

<th>Action</th>

</tr>


</thead>


<tbody>


<?php


if (mysqli_num_rows($result) > 0) {


while ($row = mysqli_fetch_assoc($result)) {


    // ======================================
    // SAFE PAYMENT METHOD
    // ======================================

    $payment_method =
        !empty($row['payment_method'])
        ? $row['payment_method']
        : "Not Available";


    // ======================================
    // SAFE PAYMENT STATUS
    // ======================================

    $payment_status =
        !empty($row['payment_status'])
        ? strtolower(trim($row['payment_status']))
        : "pending";


    // ======================================
    // ORDER STATUS
    // ======================================

    $order_status =
        strtolower(
            trim(
                $row['order_status']
            )
        );

?>


<tr>


<!-- ORDER ID -->

<td>

<strong>

#<?php echo $row['order_id']; ?>

</strong>

</td>


<!-- CUSTOMER -->

<td>

<?php

echo htmlspecialchars(
    $row['customer_name']
);

?>

</td>


<!-- EMAIL -->

<td>

<?php

echo htmlspecialchars(
    $row['email']
);

?>

</td>


<!-- TOTAL -->

<td>

<strong>

₹<?php

echo number_format(
    $row['total_amount'],
    2
);

?>

</strong>

</td>


<!-- PAYMENT METHOD -->

<td>

<?php

echo htmlspecialchars(
    $payment_method
);

?>

</td>


<!-- PAYMENT STATUS -->

<td>


<?php


if ($payment_status == "paid") {


    echo "

    <span class='badge bg-success'>

        <i class='bi bi-check-circle'></i>

        Paid

    </span>

    ";


} else {


    echo "

    <span class='badge bg-warning text-dark'>

        <i class='bi bi-clock'></i>

        Pending

    </span>

    ";

}


?>

</td>


<!-- ORDER STATUS -->

<td>


<?php


switch ($order_status) {


    case "pending":


        echo "

        <span class='badge bg-warning text-dark'>

            <i class='bi bi-clock'></i>

            Pending

        </span>

        ";

        break;



    case "processing":


        echo "

        <span class='badge bg-info text-dark'>

            <i class='bi bi-gear'></i>

            Processing

        </span>

        ";

        break;



    case "delivered":


        echo "

        <span class='badge bg-success'>

            <i class='bi bi-check-circle'></i>

            Delivered

        </span>

        ";

        break;



    case "cancelled":


        echo "

        <span class='badge bg-danger'>

            <i class='bi bi-x-circle'></i>

            Cancelled

        </span>

        ";

        break;



    default:


        echo "

        <span class='badge bg-secondary'>

            " .
            htmlspecialchars(
                $row['order_status']
            )
            . "

        </span>

        ";

        break;

}


?>

</td>


<!-- DATE -->

<td>

<?php

echo date(
    "d-m-Y",
    strtotime(
        $row['order_date']
    )
);

?>

</td>


<!-- ACTION -->

<td>


<a
href="details.php?id=<?php echo $row['order_id']; ?>"
class="btn btn-primary btn-sm mb-1">

<i class="bi bi-eye"></i>

View

</a>


<a
href="update_order.php?id=<?php echo $row['order_id']; ?>"
class="btn btn-warning btn-sm mb-1">

<i class="bi bi-pencil-square"></i>

Update

</a>


<a
href="invoice.php?id=<?php echo $row['order_id']; ?>"
class="btn btn-success btn-sm mb-1">

<i class="bi bi-printer"></i>

Invoice

</a>


</td>


</tr>


<?php


}


} else {


?>


<tr>

<td
colspan="9"
class="text-center text-danger py-4">


<i
class="bi bi-exclamation-circle"
style="font-size:30px;">
</i>


<br>


No orders found.


</td>

</tr>


<?php

}

?>


</tbody>


</table>


</div>


<!-- ==========================================
     PAGINATION
========================================== -->


<?php

if ($totalPages > 1) {

?>


<nav class="mt-4">


<ul
class="pagination justify-content-center">


<?php


for (
    $i = 1;
    $i <= $totalPages;
    $i++
) {


?>


<li
class="page-item
<?php

echo (
    $i == $page
)
? 'active'
: '';

?>">


<a
class="page-link"
href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>">


<?php echo $i; ?>


</a>


</li>


<?php


}


?>


</ul>


</nav>


<?php

}

?>


</div>

</div>

</div>


</body>

</html>