<?php
session_start();
include("config/db.php");

$message = "";

if(isset($_POST['register']))
{
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $mobile = trim($_POST['mobile_no']);
    $address = trim($_POST['address']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check empty fields
    if(empty($name) || empty($email) || empty($mobile) || empty($address) || empty($password) || empty($confirm_password))
    {
        $message = "<div class='alert alert-danger'>All fields are required.</div>";
    }
    elseif($password != $confirm_password)
    {
        $message = "<div class='alert alert-danger'>Passwords do not match.</div>";
    }
    else
    {
        // Check if email already exists
        $check = mysqli_query($conn, "SELECT * FROM customer_detail WHERE email='$email'");

        if(mysqli_num_rows($check) > 0)
        {
            $message = "<div class='alert alert-danger'>Email already registered.</div>";
        }
        else
        {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO customer_detail(name,email,mobile_no,address,password)
                    VALUES('$name','$email','$mobile','$address','$hash')";

            if(mysqli_query($conn,$sql))
            {
                $message = "<div class='alert alert-success'>
                Registration Successful.
                <a href='login.php'>Login Here</a>
                </div>";
            }
            else
            {
                $message = "<div class='alert alert-danger'>Registration Failed.</div>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Registration</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body{
            background:#f8f9fa;
        }
        .register-box{
            width:500px;
            margin:50px auto;
            padding:30px;
            background:#fff;
            border-radius:10px;
            box-shadow:0 0 10px rgba(0,0,0,.2);
        }
    </style>
</head>

<body>

<div class="register-box">

<h2 class="text-center mb-4">Sweet Shop Registration</h2>

<?php echo $message; ?>

<form method="POST">

<div class="mb-3">
<label>Full Name</label>
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
<textarea name="address" class="form-control" required></textarea>
</div>

<div class="mb-3">
<label>Password</label>
<input type="password" name="password" class="form-control" required>
</div>

<div class="mb-3">
<label>Confirm Password</label>
<input type="password" name="confirm_password" class="form-control" required>
</div>

<div class="d-grid">
<button type="submit" name="register" class="btn btn-success">
Register
</button>
</div>

<div class="text-center mt-3">
Already have an account?
<a href="login.php">Login</a>
</div>

</form>

</div>

</body>
</html>