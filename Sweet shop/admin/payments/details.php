<?php
session_start();
include("../../config/db.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: view.php");
    exit();
}

$payment_id = (int)$_GET['id'];

$sql = "SELECT
            p.payment_id,
            p.order_id,
            c.customer_id,
            c.name,
            c.email,
            c.mobile_no,
            c.address,
            p.payment_method,
            p.payment_status,
            p.payment_date,
            p.amount,
            p.transaction_id,
            o.order_status,
            o.order_date,
            o.total_amount
        FROM payment p
        INNER JOIN customer_detail c
            ON p.customer_id = c.customer_id
        LEFT JOIN `order` o
            ON p.order_id = o.order_id
        WHERE p.payment_id='$payment_id'";

$result = mysqli_query($conn,$sql);

if(mysqli_num_rows($result)==0)
{
    header("Location:view.php");
    exit();
}

$row = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Payment Details</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

<div class="card shadow">

<div class="card-header bg-primary text-white">

<h3>Payment Details</h3>

</div>

<div class="card-body">

<table class="table table-bordered">

<tr>
<th width="30%">Payment ID</th>
<td><?php echo $row['payment_id']; ?></td>
</tr>

<tr>
<th>Order ID</th>
<td>#<?php echo $row['order_id']; ?></td>
</tr>

<tr>
<th>Customer Name</th>
<td><?php echo $row['name']; ?></td>
</tr>

<tr>
<th>Email</th>
<td><?php echo $row['email']; ?></td>
</tr>

<tr>
<th>Mobile</th>
<td><?php echo $row['mobile_no']; ?></td>
</tr>

<tr>
<th>Address</th>
<td><?php echo $row['address']; ?></td>
</tr>

<tr>
<th>Payment Method</th>
<td><?php echo $row['payment_method']; ?></td>
</tr>

<tr>
<th>Payment Status</th>
<td>
<?php
if($row['payment_status']=="Paid")
{
    echo "<span class='badge bg-success'>Paid</span>";
}
else
{
    echo "<span class='badge bg-warning'>Pending</span>";
}
?>
</td>
</tr>

<tr>
<th>Payment Date</th>
<td><?php echo $row['payment_date']; ?></td>
</tr>

<tr>
<th>Amount</th>
<td>₹<?php echo number_format($row['amount'],2); ?></td>
</tr>

<tr>
<th>Transaction ID</th>
<td>
<?php
if($row['transaction_id']=="")
{
    echo "N/A";
}
else
{
    echo $row['transaction_id'];
}
?>
</td>
</tr>

<tr>
<th>Order Status</th>
<td><?php echo $row['order_status']; ?></td>
</tr>

<tr>
<th>Order Date</th>
<td><?php echo $row['order_date']; ?></td>
</tr>

</table>

<div class="mt-3">

<a href="update.php?id=<?php echo $row['payment_id']; ?>"
class="btn btn-success">

<i class="bi bi-pencil-square"></i>

Update Status

</a>

<a href="view.php"
class="btn btn-secondary">

<i class="bi bi-arrow-left"></i>

Back

</a>

</div>

</div>

</div>

</div>

</body>

</html>