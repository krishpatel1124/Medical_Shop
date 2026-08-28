<?php
session_start();
include("../../config/db.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

$message = "";

if(isset($_POST['save']))
{
    $supplier_name = mysqli_real_escape_string($conn, trim($_POST['supplier_name']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $mobile_no = mysqli_real_escape_string($conn, trim($_POST['mobile_no']));
    $address = mysqli_real_escape_string($conn, trim($_POST['address']));
    $password = mysqli_real_escape_string($conn, trim($_POST['password']));

    // Check duplicate email
    $check = mysqli_query($conn,
    "SELECT * FROM supplier_detail
     WHERE email='$email'");

    if(mysqli_num_rows($check)>0)
    {
        $message = "<div class='alert alert-danger'>
        Email already exists.
        </div>";
    }
    else
    {
        $sql = "INSERT INTO supplier_detail
        (supplier_name,email,mobile_no,address,password)
        VALUES
        ('$supplier_name','$email','$mobile_no','$address','$password')";

        if(mysqli_query($conn,$sql))
        {
            header("Location:view.php?success=1");
            exit();
        }
        else
        {
            $message = "<div class='alert alert-danger'>
            Failed to add supplier.
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

<title>Add Supplier</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-md-8">

<div class="card shadow">

<div class="card-header bg-success text-white">

<h3>

<i class="bi bi-plus-circle"></i>

Add Supplier

</h3>

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
required>

</div>

<div class="mb-3">

<label class="form-label">Email</label>

<input
type="email"
name="email"
class="form-control"
required>

</div>

<div class="mb-3">

<label class="form-label">Mobile Number</label>

<input
type="text"
name="mobile_no"
class="form-control"
maxlength="10"
required>

</div>

<div class="mb-3">

<label class="form-label">Address</label>

<textarea
name="address"
class="form-control"
rows="3"
required></textarea>

</div>

<div class="mb-3">

<label class="form-label">Password</label>

<input
type="password"
name="password"
class="form-control"
required>

</div>

<div class="d-grid">

<button
type="submit"
name="save"
class="btn btn-success">

<i class="bi bi-save"></i>

Save Supplier

</button>

</div>

</form>

<hr>

<a href="view.php" class="btn btn-secondary">

<i class="bi bi-arrow-left"></i>

Back to Suppliers

</a>

</div>

</div>

</div>

</div>

</div>

</body>

</html>