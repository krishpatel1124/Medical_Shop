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

$stock_id = (int)$_GET['id'];

$result = mysqli_query($conn,"
SELECT
    stock.stock_id,
    stock.sweet_id,
    stock.quantity_in_stock,
    stock.last_updated,
    sweets.sweet_name
FROM stock
INNER JOIN sweets
ON stock.sweet_id = sweets.sweet_id
WHERE stock.stock_id='$stock_id'
");

if(mysqli_num_rows($result)==0)
{
    header("Location:view.php");
    exit();
}

$row = mysqli_fetch_assoc($result);

$message="";

if(isset($_POST['update']))
{
    $quantity = (int)$_POST['quantity'];

    $sql = "UPDATE stock SET
            quantity_in_stock='$quantity',
            last_updated=NOW()
            WHERE stock_id='$stock_id'";

    if(mysqli_query($conn,$sql))
    {
        header("Location:view.php?updated=1");
        exit();
    }
    else
    {
        $message = "<div class='alert alert-danger'>
        Failed to update stock.
        </div>";
    }
}
?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Edit Stock</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-md-7">

<div class="card shadow">

<div class="card-header bg-warning">

<h3>Edit Stock</h3>

</div>

<div class="card-body">

<?php echo $message; ?>

<form method="POST">

<div class="mb-3">

<label class="form-label">Sweet Name</label>

<input
type="text"
class="form-control"
value="<?php echo htmlspecialchars($row['sweet_name']); ?>"
readonly>

</div>

<div class="mb-3">

<label class="form-label">

Stock Quantity

</label>

<input
type="number"
name="quantity"
class="form-control"
min="0"
value="<?php echo $row['quantity_in_stock']; ?>"
required>

</div>

<div class="mb-3">

<label class="form-label">

Last Updated

</label>

<input
type="text"
class="form-control"
value="<?php echo $row['last_updated']; ?>"
readonly>

</div>

<div class="d-grid">

<button
type="submit"
name="update"
class="btn btn-warning">

<i class="bi bi-pencil-square"></i>

Update Stock

</button>

</div>

</form>

<hr>

<a href="view.php" class="btn btn-secondary">

<i class="bi bi-arrow-left"></i>

Back to Stock

</a>

</div>

</div>

</div>

</div>

</div>

</body>

</html>