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

$supplier_id = (int)$_GET['id'];

$result = mysqli_query($conn,"
SELECT *
FROM supplier_detail
WHERE supplier_id='$supplier_id'
");

if(mysqli_num_rows($result)==0)
{
    header("Location:view.php");
    exit();
}

$row = mysqli_fetch_assoc($result);

$message = "";

if(isset($_POST['update']))
{
    $supplier_name = mysqli_real_escape_string($conn,trim($_POST['supplier_name']));
    $email = mysqli_real_escape_string($conn,trim($_POST['email']));
    $mobile_no = mysqli_real_escape_string($conn,trim($_POST['mobile_no']));
    $address = mysqli_real_escape_string($conn,trim($_POST['address']));
    $password = mysqli_real_escape_string($conn,trim($_POST['password']));

    // Check duplicate email
    $check = mysqli_query($conn,"
    SELECT *
    FROM supplier_detail
    WHERE email='$email'
    AND supplier_id<>'$supplier_id'
    ");

    if(mysqli_num_rows($check)>0)
    {
        $message = "<div class='alert alert-danger'>
        Email already exists.
        </div>";
    }
    else
    {
        $sql = "
        UPDATE supplier_detail SET
        supplier_name='$supplier_name',
        email='$email',
        mobile_no='$mobile_no',
        address='$address',
        password='$password'
        WHERE supplier_id='$supplier_id'
        ";

        if(mysqli_query($conn,$sql))
        {
            header("Location:view.php?updated=1");
            exit();
        }
        else
        {
            $message = "<div class='alert alert-danger'>
            Failed to update supplier.
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

<title>Edit Supplier</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-md-8">

<div class="card shadow">

<div class="card-header bg-warning">

<h3>

<i class="bi bi-pencil-square"></i>

Edit Supplier

</h3>

</div>

<div class="card-body">

<?php echo $message; ?>

<form method="POST">

<div class="mb-3">

<label class="form-label">

Supplier Name

</label>

<input
type="text"
name="supplier_name"
class="form-control"
value="<?php echo htmlspecialchars($row['supplier_name']); ?>"
required>

</div>

<div class="mb-3">

<label class="form-label">

Email

</label>

<input
type="email"
name="email"
class="form-control"
value="<?php echo htmlspecialchars($row['email']); ?>"
required>

</div>

<div class="mb-3">

<label class="form-label">

Mobile Number

</label>

<input
type="text"
name="mobile_no"
class="form-control"
maxlength="10"
value="<?php echo htmlspecialchars($row['mobile_no']); ?>"
required>

</div>

<div class="mb-3">

<label class="form-label">

Address

</label>

<textarea
name="address"
class="form-control"
rows="3"
required><?php echo htmlspecialchars($row['address']); ?></textarea>

</div>

<div class="mb-3">

<label class="form-label">

Password

</label>

<input
type="text"
name="password"
class="form-control"
value="<?php echo htmlspecialchars($row['password']); ?>"
required>

</div>

<div class="d-grid">

<button
type="submit"
name="update"
class="btn btn-warning">

<i class="bi bi-save"></i>

Update Supplier

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