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

$customer_id = (int)$_GET['id'];

$result = mysqli_query($conn,
"SELECT * FROM customer_detail
 WHERE customer_id='$customer_id'");

if(mysqli_num_rows($result)==0)
{
    header("Location: view.php");
    exit();
}

$row = mysqli_fetch_assoc($result);

$message = "";

if(isset($_POST['update']))
{
    $name = mysqli_real_escape_string($conn,$_POST['name']);
    $email = mysqli_real_escape_string($conn,$_POST['email']);
    $mobile = mysqli_real_escape_string($conn,$_POST['mobile_no']);
    $address = mysqli_real_escape_string($conn,$_POST['address']);
    
    // Check duplicate email
    $check = mysqli_query($conn,
    "SELECT * FROM customer_detail
     WHERE email='$email'
     AND customer_id!='$customer_id'");

    if(mysqli_num_rows($check)>0)
    {
        $message = "<div class='alert alert-danger'>
        Email already exists.
        </div>";
    }
    else
    {
        $sql = "UPDATE customer_detail SET
                name='$name',
                email='$email',
                mobile_no='$mobile',
                address='$address'
                WHERE customer_id='$customer_id'";

        if(mysqli_query($conn,$sql))
        {
            header("Location:view.php?updated=1");
            exit();
        }
        else
        {
            $message = "<div class='alert alert-danger'>
            Update failed.
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

<title>Edit Customer</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-md-8">

<div class="card shadow">

<div class="card-header bg-warning">

<h3>Edit Customer</h3>

</div>

<div class="card-body">

<?php echo $message; ?>

<form method="POST">

<div class="mb-3">
<label>Name</label>
<input
type="text"
name="name"
class="form-control"
value="<?php echo ($row['name']); ?>"
required>
</div>

<div class="mb-3">
<label>Email</label>
<input
type="email"
name="email"
class="form-control"
value="<?php echo ($row['email']); ?>"
required>
</div>

<div class="mb-3">
<label>Mobile Number</label>
<input
type="text"
name="mobile_no"
class="form-control"
maxlength="10"
value="<?php echo ($row['mobile_no']); ?>"
required>
</div>

<div class="mb-3">
<label>Address</label>
<textarea
name="address"
class="form-control"
rows="3"
required><?php echo ($row['address']); ?></textarea>
</div>



<div class="d-grid">

<button
type="submit"
name="update"
class="btn btn-warning">

<i class="bi bi-pencil-square"></i>

Update Customer

</button>

</div>

</form>

<hr>

<a href="view.php" class="btn btn-secondary">

<i class="bi bi-arrow-left"></i>

Back to Customers

</a>

</div>

</div>

</div>

</div>

</div>

</body>

</html>