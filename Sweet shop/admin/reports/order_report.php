<?php

session_start();
include("../../config/db.php");

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);


// =====================================================
// ADMIN LOGIN
// =====================================================

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}


// =====================================================
// FILTERS
// =====================================================

$search = isset($_GET['search'])
    ? trim($_GET['search'])
    : "";

$status = isset($_GET['status'])
    ? trim($_GET['status'])
    : "";

$from_date = isset($_GET['from_date'])
    ? trim($_GET['from_date'])
    : "";

$to_date = isset($_GET['to_date'])
    ? trim($_GET['to_date'])
    : "";


// =====================================================
// BUILD WHERE
// =====================================================

$where = " WHERE 1=1 ";

$params = [];
$types = "";


// Search
if ($search != "") {

    $where .= "
        AND (
            o.order_id LIKE ?
            OR c.name LIKE ?
            OR c.email LIKE ?
        )
    ";

    $searchValue = "%" . $search . "%";

    $params[] = $searchValue;
    $params[] = $searchValue;
    $params[] = $searchValue;

    $types .= "sss";
}


// Order status
if ($status != "") {

    $where .= " AND LOWER(o.order_status) = LOWER(?) ";

    $params[] = $status;

    $types .= "s";
}


// From date
if ($from_date != "") {

    $where .= " AND o.order_date >= ? ";

    $params[] = $from_date;

    $types .= "s";
}


// To date
if ($to_date != "") {

    $where .= " AND o.order_date <= ? ";

    $params[] = $to_date;

    $types .= "s";
}


// =====================================================
// MAIN ORDER QUERY
// =====================================================

$sql = "

SELECT

    o.order_id,
    o.customer_id,
    o.order_date,
    o.total_amount,
    o.order_status,

    c.name AS customer_name,
    c.email AS customer_email,
    c.mobile_no AS customer_phone,

    p.payment_method,
    p.payment_status

FROM `order` o

LEFT JOIN customer_detail c
    ON o.customer_id = c.customer_id

LEFT JOIN payment p
    ON o.order_id = p.order_id

$where

ORDER BY o.order_id DESC

";

$stmt = mysqli_prepare($conn, $sql);

if (!empty($params)) {

    mysqli_stmt_bind_param(
        $stmt,
        $types,
        ...$params
    );
}

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);


// =====================================================
// SUMMARY
// =====================================================

$total_orders = 0;
$total_sales = 0;
$delivered_orders = 0;
$pending_orders = 0;
$processing_orders = 0;
$cancelled_orders = 0;
$paid_orders = 0;
$pending_payment = 0;


$orders = [];

while ($row = mysqli_fetch_assoc($result)) {

    $orders[] = $row;

    $total_orders++;

    $total_sales += (float)$row['total_amount'];


    $orderStatus = strtolower(
        trim($row['order_status'] ?? '')
    );


    if ($orderStatus == "delivered") {

        $delivered_orders++;

    } elseif ($orderStatus == "pending") {

        $pending_orders++;

    } elseif ($orderStatus == "processing") {

        $processing_orders++;

    } elseif ($orderStatus == "cancelled") {

        $cancelled_orders++;
    }


    $paymentStatus = strtolower(
        trim($row['payment_status'] ?? '')
    );


    if ($paymentStatus == "paid") {

        $paid_orders++;

    } else {

        $pending_payment++;
    }
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1">

<title>Order Report - Sweet Shop</title>


<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">


<style>

body {
    background: #f5f6fa;
}

.report-card {
    border: none;
    border-radius: 12px;
}

.summary-card {
    border-radius: 12px;
    border: none;
    box-shadow: 0 3px 12px rgba(0,0,0,.08);
}

.summary-number {
    font-size: 28px;
    font-weight: bold;
}

.report-title {
    font-weight: bold;
}

@media print {

    .no-print {
        display: none !important;
    }

    body {
        background: white;
    }

    .card {
        box-shadow: none !important;
    }

}

</style>

</head>


<body>


<div class="container-fluid py-4">


<!-- =====================================================
     HEADER
===================================================== -->

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h2 class="report-title">
            📊 Order Report
        </h2>

        <p class="text-muted mb-0">
            Sweet Shop - Order Management Report
        </p>

    </div>


    <div class="no-print">

        <a href="../dashboard.php"
           class="btn btn-secondary">

            ← Dashboard

        </a>

        <button
            onclick="window.print()"
            class="btn btn-primary">

            🖨 Print

        </button>

        <button
            onclick="exportTable()"
            class="btn btn-success">

            📥 Export CSV

        </button>

    </div>

</div>


<!-- =====================================================
     SUMMARY CARDS
===================================================== -->

<div class="row g-3 mb-4">


<div class="col-lg-3 col-md-6">

<div class="card summary-card">

<div class="card-body">

<h6 class="text-muted">
Total Orders
</h6>

<div class="summary-number text-primary">

<?php echo $total_orders; ?>

</div>

</div>

</div>

</div>


<div class="col-lg-3 col-md-6">

<div class="card summary-card">

<div class="card-body">

<h6 class="text-muted">
Total Sales
</h6>

<div class="summary-number text-success">

₹<?php echo number_format($total_sales, 2); ?>

</div>

</div>

</div>

</div>


<div class="col-lg-3 col-md-6">

<div class="card summary-card">

<div class="card-body">

<h6 class="text-muted">
Delivered
</h6>

<div class="summary-number text-success">

<?php echo $delivered_orders; ?>

</div>

</div>

</div>

</div>


<div class="col-lg-3 col-md-6">

<div class="card summary-card">

<div class="card-body">

<h6 class="text-muted">
Cancelled
</h6>

<div class="summary-number text-danger">

<?php echo $cancelled_orders; ?>

</div>

</div>

</div>

</div>

</div>


<!-- =====================================================
     MORE SUMMARY
===================================================== -->

<div class="row g-3 mb-4">


<div class="col-md-3">

<div class="card summary-card">

<div class="card-body">

<h6 class="text-muted">
Pending Orders
</h6>

<h4 class="text-warning">

<?php echo $pending_orders; ?>

</h4>

</div>

</div>

</div>


<div class="col-md-3">

<div class="card summary-card">

<div class="card-body">

<h6 class="text-muted">
Processing
</h6>

<h4 class="text-info">

<?php echo $processing_orders; ?>

</h4>

</div>

</div>

</div>


<div class="col-md-3">

<div class="card summary-card">

<div class="card-body">

<h6 class="text-muted">
Paid Orders
</h6>

<h4 class="text-success">

<?php echo $paid_orders; ?>

</h4>

</div>

</div>

</div>


<div class="col-md-3">

<div class="card summary-card">

<div class="card-body">

<h6 class="text-muted">
Pending Payment
</h6>

<h4 class="text-warning">

<?php echo $pending_payment; ?>

</h4>

</div>

</div>

</div>

</div>


<!-- =====================================================
     FILTER
===================================================== -->

<div class="card report-card shadow mb-4 no-print">


<div class="card-header bg-primary text-white">

<h5 class="mb-0">
Filter Orders
</h5>

</div>


<div class="card-body">


<form method="GET">


<div class="row g-3">


<div class="col-md-3">

<label class="form-label">
Search
</label>

<input
type="text"
name="search"
class="form-control"
placeholder="Order ID / Customer / Email"
value="<?php
echo htmlspecialchars($search);
?>">

</div>


<div class="col-md-2">

<label class="form-label">
Order Status
</label>

<select
name="status"
class="form-select">

<option value="">
All Status
</option>


<option value="Pending"
<?php

if (strtolower($status) == "pending") {
    echo "selected";
}

?>>

Pending

</option>


<option value="Processing"
<?php

if (strtolower($status) == "processing") {
    echo "selected";
}

?>>

Processing

</option>


<option value="Delivered"
<?php

if (strtolower($status) == "delivered") {
    echo "selected";
}

?>>

Delivered

</option>


<option value="Cancelled"
<?php

if (strtolower($status) == "cancelled") {
    echo "selected";
}

?>>

Cancelled

</option>

</select>

</div>


<div class="col-md-2">

<label class="form-label">
From Date
</label>

<input
type="date"
name="from_date"
class="form-control"
value="<?php
echo htmlspecialchars($from_date);
?>">

</div>


<div class="col-md-2">

<label class="form-label">
To Date
</label>

<input
type="date"
name="to_date"
class="form-control"
value="<?php
echo htmlspecialchars($to_date);
?>">

</div>


<div class="col-md-1 d-flex align-items-end">

<button
type="submit"
class="btn btn-primary w-100">

Filter

</button>

</div>


<div class="col-md-2 d-flex align-items-end">

<a
href="order.php"
class="btn btn-secondary w-100">

Reset

</a>

</div>


</div>

</form>


</div>

</div>


<!-- =====================================================
     ORDER TABLE
===================================================== -->

<div class="card report-card shadow">


<div class="card-header bg-dark text-white">

<h5 class="mb-0">
Order Details
</h5>

</div>


<div class="card-body">


<div class="table-responsive">


<table
id="orderTable"
class="table table-bordered table-hover align-middle">


<thead class="table-dark">

<tr>

<th>#</th>

<th>Order ID</th>

<th>Customer</th>

<th>Email</th>

<th>Phone</th>

<th>Order Date</th>

<th>Total Amount</th>

<th>Payment Method</th>

<th>Payment Status</th>

<th>Order Status</th>

<th class="no-print">
Action
</th>

</tr>

</thead>


<tbody>


<?php

if (count($orders) > 0) {

    $serial = 1;

    foreach ($orders as $row) {

?>


<tr>


<td>

<?php echo $serial++; ?>

</td>


<td>

<strong>

#<?php echo $row['order_id']; ?>

</strong>

</td>


<td>

<?php

echo htmlspecialchars(
    $row['customer_name'] ?? 'Not Available'
);

?>

</td>


<td>

<?php

echo htmlspecialchars(
    $row['customer_email'] ?? 'Not Available'
);

?>

</td>


<td>

<?php

echo htmlspecialchars(
    $row['customer_phone'] ?? 'Not Available'
);

?>

</td>


<td>

<?php

echo htmlspecialchars(
    $row['order_date']
);

?>

</td>


<td>

<strong class="text-success">

₹<?php

echo number_format(
    $row['total_amount'],
    2
);

?>

</strong>

</td>


<td>

<?php

echo htmlspecialchars(
    $row['payment_method'] ?? 'Not Available'
);

?>

</td>


<td>


<?php

$paymentStatus = strtolower(
    trim($row['payment_status'] ?? '')
);


if ($paymentStatus == "paid") {

    echo '<span class="badge bg-success">
            Paid
          </span>';

} elseif ($paymentStatus == "pending") {

    echo '<span class="badge bg-warning text-dark">
            Pending
          </span>';

} else {

    echo '<span class="badge bg-secondary">
            Not Available
          </span>';

}

?>


</td>


<td>


<?php

$orderStatus = strtolower(
    trim($row['order_status'] ?? '')
);


if ($orderStatus == "pending") {

    echo '<span class="badge bg-warning text-dark">
            Pending
          </span>';

} elseif ($orderStatus == "processing") {

    echo '<span class="badge bg-info">
            Processing
          </span>';

} elseif ($orderStatus == "delivered") {

    echo '<span class="badge bg-success">
            Delivered
          </span>';

} elseif ($orderStatus == "cancelled") {

    echo '<span class="badge bg-danger">
            Cancelled
          </span>';

} else {

    echo '<span class="badge bg-secondary">
            ' .
            htmlspecialchars(
                $row['order_status']
            )
            .
            '
          </span>';

}

?>


</td>


<td class="no-print">


<a
href="../orders/details.php?id=<?php
echo $row['order_id'];
?>"
class="btn btn-primary btn-sm">

View

</a>


<a
href="../orders/invoice.php?id=<?php
echo $row['order_id'];
?>"
class="btn btn-success btn-sm">

Invoice

</a>


</td>


</tr>


<?php

    }

} else {

?>


<tr>

<td colspan="11"
    class="text-center text-danger py-4">

<h5>
No orders found
</h5>

<p class="mb-0">
Try changing your filters.
</p>

</td>

</tr>


<?php

}

?>


</tbody>


<tfoot>

<tr>

<th colspan="6"
    class="text-end">

Total Sales

</th>

<th class="text-success">

₹<?php

echo number_format(
    $total_sales,
    2
);

?>

</th>

<th colspan="4"></th>

</tr>

</tfoot>


</table>


</div>


</div>

</div>


</div>


<script>

function exportTable() {

    let table =
        document.getElementById("orderTable");

    let rows =
        table.querySelectorAll("tr");

    let csv = [];


    rows.forEach(function(row) {

        let cols =
            row.querySelectorAll("th, td");

        let data = [];


        cols.forEach(function(col) {

            if (
                col.classList.contains("no-print")
            ) {
                return;
            }

            let text =
                col.innerText
                .replace(/"/g, '""')
                .replace(/\n/g, ' ')
                .trim();

            data.push('"' + text + '"');

        });


        csv.push(data.join(","));

    });


    let blob =
        new Blob(
            [csv.join("\n")],
            {
                type: "text/csv;charset=utf-8;"
            }
        );


    let url =
        URL.createObjectURL(blob);


    let link =
        document.createElement("a");


    link.href = url;

    link.download =
        "order_report.csv";


    link.click();


    URL.revokeObjectURL(url);

}

</script>


</body>

</html>