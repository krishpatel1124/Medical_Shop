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

$supplier = mysqli_fetch_assoc($query);

$message = "";

if(isset($_POST['change_password']))
{
    $current_password = trim($_POST['current_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    if($current_password != $supplier['password'])
    {
        $message = "<div class='alert alert-danger'>
        Current password is incorrect.
        </div>";
    }
    elseif($new_password != $confirm_password)
    {
        $message = "<div class='alert alert-danger'>
        New password and Confirm password do not match.
        </div>";
    }
    elseif(strlen($new_password) < 6)
    {
        $message = "<div class='alert alert-warning'>
        Password must be at least 6 characters long.
        </div>";
    }
    else
    {
        $sql = "
        UPDATE supplier_detail
        SET password='$new_password'
        WHERE supplier_id='$supplier_id'
        ";

        if(mysqli_query($conn,$sql))
        {
            $message = "<div class='alert alert-success'>
            Password changed successfully.
            </div>";
        }
        else
        {
            $message = "<div class='alert alert-danger'>
            Failed to change password.
            </div>";
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Change Password</title>

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

</style>

</head>

<body>

<div class="sidebar">

<h3>Supplier Panel</h3>

<a href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>

<a href="profile.php"><i class="bi bi-person"></i> My Profile</a>

<a href="edit_profile.php"><i class="bi bi-pencil-square"></i> Edit Profile</a>

<a href="sweets/view.php"><i class="bi bi-box"></i> My Sweets</a>

<a href="stock/view.php"><i class="bi bi-archive"></i> Stock</a>

<a href="orders/view.php"><i class="bi bi-bag"></i> Orders</a>

<a href="change_password.php"><i class="bi bi-key"></i> Change Password</a>

<a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>

</div>

<div class="main">

<div class="topbar d-flex justify-content-between">

<h3>Change Password</h3>

<div>

Welcome,

<b><?php echo htmlspecialchars($supplier['supplier_name']); ?></b>

</div>

</div>

<div class="content">

<div class="row justify-content-center">

<div class="col-lg-6">

<div class="card">

<div class="card-header bg-primary text-white">

<h4>Change Password</h4>

</div>

<div class="card-body">

<?php echo $message; ?>

<form method="POST">

<div class="mb-3">

<label class="form-label">Current Password</label>

<input
type="password"
name="current_password"
class="form-control"
required>

</div>

<div class="mb-3">

<label class="form-label">New Password</label>

<input
type="password"
name="new_password"
class="form-control"
required>

</div>

<div class="mb-3">

<label class="form-label">Confirm New Password</label>

<input
type="password"
name="confirm_password"
class="form-control"
required>

</div>

<div class="d-grid">

<button
type="submit"
name="change_password"
class="btn btn-primary">

<i class="bi bi-key-fill"></i>

Change Password

</button>

</div>

</form>

<hr>

<a href="profile.php" class="btn btn-secondary">

<i class="bi bi-arrow-left"></i>

Back to Profile

</a>

</div>

</div>

</div>

</div>

</div>

</div>

</body>

</html>