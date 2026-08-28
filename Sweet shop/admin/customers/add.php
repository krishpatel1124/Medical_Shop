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
    $name = mysqli_real_escape_string($conn,$_POST['name']);
    $email = mysqli_real_escape_string($conn,$_POST['email']);
    $mobile = mysqli_real_escape_string($conn,$_POST['mobile_no']);
    $address = mysqli_real_escape_string($conn,$_POST['address']);
    $password = mysqli_real_escape_string($conn,$_POST['password']);

    // Check duplicate email
    $check = mysqli_query($conn,
    "SELECT * FROM customer_detail
     WHERE email='$email'");

    if(mysqli_num_rows($check)>0)
    {
        $message = "<div class='alert alert-danger'>
        Email already exists.
        </div>";
    }
    else
    {
        $sql = "INSERT INTO customer_detail
        (name,email,mobile_no,address,password)
        VALUES
        ('$name','$email','$mobile','$address','$password')";

        if(mysqli_query($conn,$sql))
        {
            header("Location:view.php?success=1");
            exit();
        }
        else
        {
            $message = "<div class='alert alert-danger'>
            Failed to add customer.
            </div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Add Customer</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-md-8">

<div class="card shadow">

<div class="card-header bg-success text-white">

<h3>Add Customer</h3>

</div>

<div class="card-body">

<?php echo $message; ?>

<form method="POST">

<div class="mb-3">
<label>Name</label>
<input type="text" name="name" class="form-control" required>
</div>

<div class="mb-3">
<label>Email</label>
<input type="email" name="email" class="form-control" required>
</div>

<div class="mb-3">
<label>Mobile Number</label>
<input type="text" name="mobile_no" class="form-control" maxlength="10" required>
</div>

<div class="mb-3">
<label>Address</label>
<textarea name="address" class="form-control" rows="3" required></textarea>
</div>

<div class="mb-3">
<label>Password</label>
<input type="password" name="password" class="form-control" required>
</div>

<div class="d-grid">
<button type="submit" name="save" class="btn btn-success">
<i class="bi bi-save"></i> Save Customer
</button>
</div>

</form>

<hr>

<a href="view.php" class="btn btn-secondary">
<i class="bi bi-arrow-left"></i> Back to Customers
</a>

</div>

</div>

</div>

</div>

</div>

</body>

</html>