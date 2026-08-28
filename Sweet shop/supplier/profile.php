<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['supplier_id'])) {
    header("Location: ../login.php");
    exit();
}

$supplier_id = $_SESSION['supplier_id'];

$query = mysqli_query($conn,"
SELECT *
FROM supplier_detail
WHERE supplier_id='$supplier_id'
");

if(mysqli_num_rows($query)==0)
{
    header("Location: logout.php");
    exit();
}

$row = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Supplier Profile</title>

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

.card{
border:none;
box-shadow:0 2px 10px rgba(0,0,0,.1);
}

.profile-icon{
font-size:90px;
color:#198754;
text-align:center;
}

</style>

</head>

<body>

<div class="sidebar">

<h3>Supplier Panel</h3>

<a href="dashboard.php">
<i class="bi bi-speedometer2"></i>
Dashboard
</a>

<a href="profile.php">
<i class="bi bi-person"></i>
My Profile
</a>

<a href="edit_profile.php">
<i class="bi bi-pencil-square"></i>
Edit Profile
</a>

<a href="sweets/view.php">
<i class="bi bi-box"></i>
My Sweets
</a>

<a href="stock/view.php">
<i class="bi bi-archive"></i>
Stock
</a>

<a href="orders/view.php">
<i class="bi bi-bag"></i>
Orders
</a>

<a href="change_password.php">
<i class="bi bi-key"></i>
Change Password
</a>

<a href="logout.php">
<i class="bi bi-box-arrow-right"></i>
Logout
</a>

</div>

<div class="main">

<div class="topbar d-flex justify-content-between">

<h3>Supplier Profile</h3>

<div>

Welcome,

<b><?php echo htmlspecialchars($row['supplier_name']); ?></b>

</div>

</div>

<div class="content">

<div class="row justify-content-center">

<div class="col-lg-8">

<div class="card">

<div class="card-header bg-success text-white">

<h4 class="mb-0">

Supplier Information

</h4>

</div>

<div class="card-body">

<div class="text-center mb-4">

<i class="bi bi-person-circle profile-icon"></i>

<h3 class="mt-3">

<?php echo htmlspecialchars($row['supplier_name']); ?>

</h3>

</div>

<table class="table table-bordered">

<tr>

<th width="35%">Supplier ID</th>

<td><?php echo $row['supplier_id']; ?></td>

</tr>

<tr>

<th>Supplier Name</th>

<td><?php echo htmlspecialchars($row['supplier_name']); ?></td>

</tr>

<tr>

<th>Email</th>

<td><?php echo htmlspecialchars($row['email']); ?></td>

</tr>

<tr>

<th>Mobile Number</th>

<td><?php echo htmlspecialchars($row['mobile_no']); ?></td>

</tr>

<tr>

<th>Address</th>

<td><?php echo nl2br(htmlspecialchars($row['address'])); ?></td>

</tr>

</table>

<div class="mt-4 text-center">

<a href="edit_profile.php"
class="btn btn-warning">

<i class="bi bi-pencil-square"></i>

Edit Profile

</a>

<a href="change_password.php"
class="btn btn-primary">

<i class="bi bi-key"></i>

Change Password

</a>

<a href="dashboard.php"
class="btn btn-secondary">

<i class="bi bi-arrow-left"></i>

Dashboard

</a>

</div>

</div>

</div>

</div>

</div>

</div>

</div>

</body>

</html>