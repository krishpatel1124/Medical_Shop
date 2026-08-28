<?php
session_start();
include("../../config/db.php");

if(!isset($_SESSION['admin_id']))
{
    header("Location: ../login.php");
    exit();
}

$search="";

if(isset($_GET['search']))
{
    $search=mysqli_real_escape_string($conn,$_GET['search']);
}

$limit=10;

$page=isset($_GET['page'])?(int)$_GET['page']:1;

if($page<1)
$page=1;

$start=($page-1)*$limit;

$count="SELECT COUNT(*) AS total
        FROM payment p
        INNER JOIN customer_detail c
        ON p.customer_id=c.customer_id";

if($search!="")
{
$count.=" WHERE
c.name LIKE '%$search%'
OR p.payment_method LIKE '%$search%'
OR p.payment_status LIKE '%$search%'";
}

$countResult=mysqli_query($conn,$count);
$total=mysqli_fetch_assoc($countResult);

$totalPages=ceil($total['total']/$limit);

$sql="SELECT
p.payment_id,
p.order_id,
c.name,
p.payment_method,
p.payment_status,
p.payment_date,
p.amount,
p.transaction_id
FROM payment p
INNER JOIN customer_detail c
ON p.customer_id=c.customer_id";

if($search!="")
{
$sql.=" WHERE
c.name LIKE '%$search%'
OR p.payment_method LIKE '%$search%'
OR p.payment_status LIKE '%$search%'";
}

$sql.=" ORDER BY p.payment_id DESC
LIMIT $start,$limit";

$result=mysqli_query($conn,$sql);
?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<title>Payments</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-4">

<div class="d-flex justify-content-between mb-3">

<h2>Payments</h2>

<a href="../dashboard.php" class="btn btn-secondary">
Back
</a>

</div>

<form method="GET" class="row mb-3">

<div class="col-md-10">

<input
type="text"
name="search"
class="form-control"
placeholder="Search Customer / Payment Method"
value="<?php echo $search;?>">

</div>

<div class="col-md-2">

<button class="btn btn-primary w-100">

Search

</button>

</div>

</form>

<div class="card shadow">

<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead class="table-dark">

<tr>

<th>ID</th>

<th>Order</th>

<th>Customer</th>

<th>Method</th>

<th>Amount</th>

<th>Status</th>

<th>Date</th>

<th>Transaction</th>

<th>Action</th>

</tr>

</thead>

<tbody>

<?php

if(mysqli_num_rows($result)>0)
{

while($row=mysqli_fetch_assoc($result))
{

?>

<tr>

<td><?php echo $row['payment_id']; ?></td>

<td>#<?php echo $row['order_id']; ?></td>

<td><?php echo $row['name']; ?></td>

<td><?php echo $row['payment_method']; ?></td>

<td>₹<?php echo number_format($row['amount'],2); ?></td>

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

<td><?php echo $row['payment_date']; ?></td>

<td><?php echo $row['transaction_id']; ?></td>

<td>

<a
href="delete.php?id=<?php echo $row['payment_id'];?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Delete Payment?')">

Delete

</a>

</td>

</tr>

<?php

}

}
else
{

?>

<tr>

<td colspan="9" class="text-center">

No Payments Found

</td>

</tr>

<?php

}

?>

</tbody>

</table>

</div>

<nav>

<ul class="pagination justify-content-center">

<?php

for($i=1;$i<=$totalPages;$i++)
{

?>

<li class="page-item <?php if($page==$i) echo 'active';?>">

<a class="page-link"
href="?page=<?php echo $i;?>&search=<?php echo urlencode($search);?>">

<?php echo $i;?>

</a>

</li>

<?php

}

?>

</ul>

</nav>

</div>

</div>

</div>

</body>

</html>