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

$message="";

if(isset($_POST['update']))
{
    $supplier_name = mysqli_real_escape_string($conn,trim($_POST['supplier_name']));
    $email = mysqli_real_escape_string($conn,trim($_POST['email']));
    $mobile_no = mysqli_real_escape_string($conn,trim($_POST['mobile_no']));
    $address = mysqli_real_escape_string($conn,trim($_POST['address']));

    // Check duplicate email
    $check = mysqli_query($conn,"
    SELECT *
    FROM supplier_detail
    WHERE email='$email'
    AND supplier_id<>'$supplier_id'
    ");

    if(mysqli_num_rows($check)>0)
    {
        $message="<div class='alert alert-danger'>
        Email already exists.
        </div>";
    }
    else
    {
        $sql="
        UPDATE supplier_detail SET
        supplier_name='$supplier_name',
        email='$email',
        mobile_no='$mobile_no',
        address='$address'
        WHERE supplier_id='$supplier_id'
        ";

        if(mysqli_query($conn,$sql))
        {
            $_SESSION['supplier_name']=$supplier_name;

            header("Location: profile.php?updated=1");
            exit();
        }
        else
        {
            $message="<div class='alert alert-danger'>
            Failed to update profile.
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

<title>Edit Profile</title>

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

<h3>Edit Profile</h3>

<div>

Welcome,

<b><?php echo htmlspecialchars($row['supplier_name']); ?></b>

</div>

</div>

<div class="content">

<div class="row justify-content-center">

<div class="col-lg-8">

<div class="card">

<div class="card-header bg-warning">

<h4>Edit Supplier Profile</h4>

</div>

<div class="card-body">

<?php echo $message; ?>

<form method="POST">

<div class="mb-3">

<label class="form-label">Supplier Name</label>

<input
type="text"
name="supplier_name"
class="form-control"
value="<?php echo htmlspecialchars($row['supplier_name']); ?>"
required>

</div>

<div class="mb-3">

<label class="form-label">Email</label>

<input
type="email"
name="email"
class="form-control"
value="<?php echo htmlspecialchars($row['email']); ?>"
required>

</div>

<div class="mb-3">

<label class="form-label">Mobile Number</label>

<input
type="text"
name="mobile_no"
class="form-control"
maxlength="10"
value="<?php echo htmlspecialchars($row['mobile_no']); ?>"
required>

</div>

<div class="mb-3">

<label class="form-label">Address</label>

<textarea
name="address"
class="form-control"
rows="4"
required><?php echo htmlspecialchars($row['address']); ?></textarea>

</div>

<div class="d-grid">

<button
type="submit"
name="update"
class="btn btn-warning">

<i class="bi bi-save"></i>

Update Profile

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
