<?php
session_start();
include("../../config/db.php");

if (!isset($_SESSION['supplier_id'])) {
    header("Location: ../../login.php");
    exit();
}

$supplier_id = $_SESSION['supplier_id'];

$search = "";

if(isset($_GET['search']))
{
    $search = mysqli_real_escape_string($conn,$_GET['search']);
}

$limit = 10;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if($page < 1)
$page = 1;

$start = ($page-1)*$limit;

/* Total Records */

$count = "
SELECT COUNT(*) AS total
FROM stock
INNER JOIN sweets
ON stock.sweet_id = sweets.sweet_id
WHERE stock.supplier_id = '$supplier_id'
";

if($search!="")
{
    $count .= " AND sweets.sweet_name LIKE '%$search%'";
}

$countResult = mysqli_query($conn,$count);
$total = mysqli_fetch_assoc($countResult);
$totalPages = ceil($total['total']/$limit);

/* Stock List */

$sql = "
SELECT
    stock.stock_id,
    stock.sweet_id,
    stock.supplier_id,
    stock.quantity_in_stock,
    stock.reorder_level,
    stock.last_updated,
    sweets.sweet_name,
    sweets.image,
    sweets.price,
    sweets.weight
FROM stock
INNER JOIN sweets
ON stock.sweet_id = sweets.sweet_id
WHERE stock.supplier_id = '$supplier_id'
";
if($search!="")
{
    $sql .= " AND sweets.sweet_name LIKE '%$search%'";
}

$sql .= "
ORDER BY sweets.sweet_name ASC
LIMIT $start,$limit
";

$result = mysqli_query($conn,$sql);
?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Stock Management</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>

body{
background:#f5f5f5;
}

.sidebar{
width:240px;
height:100vh;
background:#198754;
position:fixed;
left:0;
top:0;
}

.sidebar h3{
color:#fff;
padding:20px;
text-align:center;
}

.sidebar a{
display:block;
padding:15px 20px;
color:#fff;
text-decoration:none;
}

.sidebar a:hover{
background:#157347;
}

.main{
margin-left:240px;
}

.topbar{
background:#fff;
padding:15px 25px;
box-shadow:0 2px 10px rgba(0,0,0,.1);
}

.content{
padding:30px;
}

.low-stock{
color:red;
font-weight:bold;
}

</style>

</head>

<body>

<div class="sidebar">

<h3>Supplier Panel</h3>

<a href="../dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>

<a href="../profile.php"><i class="bi bi-person"></i> Profile</a>

<a href="../sweets/view.php"><i class="bi bi-box"></i> My Sweets</a>

<a href="view.php"><i class="bi bi-archive"></i> Stock</a>

<a href="../orders/view.php"><i class="bi bi-bag"></i> Orders</a>

<a href="../change_password.php"><i class="bi bi-key"></i> Change Password</a>

<a href="../logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>

</div>

<div class="main">

<div class="topbar d-flex justify-content-between">

<h3>Stock Management</h3>

</div>

<div class="content">

<form method="GET" class="row mb-3">

<div class="col-md-10">

<input
type="text"
name="search"
class="form-control"
placeholder="Search Sweet"
value="<?php echo htmlspecialchars($search); ?>">

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

<th>Image</th>

<th>Sweet Name</th>

<th>Stock</th>

<th>Last Updated</th>

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

<td>

<img
src="../../uploads/sweets/<?php echo $row['image']; ?>"
width="70"
height="70"
style="object-fit:cover;border-radius:8px;">

</td>

<td>

<?php echo htmlspecialchars($row['sweet_name']); ?>

</td>

<td>

<?php

if($row['quantity_in_stock']<=10)
{
?>

<span class="low-stock">

<?php echo $row['quantity_in_stock']; ?>

(Low)

</span>

<?php
}
else
{
echo $row['quantity_in_stock'];
}

?>

</td>

<td>

<?php echo $row['last_updated']; ?>

</td>

<td>

<a
href="update.php?id=<?php echo $row['stock_id']; ?>"
class="btn btn-warning btn-sm">

<i class="bi bi-pencil-square"></i>

Update

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

<td colspan="5" class="text-center">

No stock records found.

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

<li class="page-item <?php if($page==$i) echo 'active'; ?>">

<a class="page-link"
href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>">

<?php echo $i; ?>

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

</div>

</body>

</html>