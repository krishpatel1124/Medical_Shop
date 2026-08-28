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

$payment_id = (int)$_GET['id'];

$result = mysqli_query($conn,"
SELECT
    p.*,
    c.name
FROM payment p
INNER JOIN customer_detail c
ON p.customer_id = c.customer_id
WHERE p.payment_id='$payment_id'
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
    $payment_method = mysqli_real_escape_string($conn,$_POST['payment_method']);
    $payment_status = mysqli_real_escape_string($conn,$_POST['payment_status']);
    $transaction_id = mysqli_real_escape_string($conn,$_POST['transaction_id']);

    $sql = "UPDATE payment SET
            payment_method='$payment_method',
            payment_status='$payment_status',
            transaction_id='$transaction_id'
            WHERE payment_id='$payment_id'";

    if(mysqli_query($conn,$sql))
    {
        // If payment is paid, update order status
        if($payment_status=="Paid")
        {
            mysqli_query($conn,"
            UPDATE `order`
            SET order_status='Processing'
            WHERE order_id='".$row['order_id']."'
            ");
        }

        header("Location:view.php?updated=1");
        exit();
    }
    else
    {
        $message="<div class='alert alert-danger'>
        Failed to update payment.
        </div>";
    }
}
?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<title>Update Payment</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-md-7">

<div class="card shadow">

<div class="card-header bg-success text-white">

<h3>Update Payment</h3>

</div>

<div class="card-body">

<?php echo $message; ?>

<form method="POST">

<div class="mb-3">

<label>Customer</label>

<input
type="text"
class="form-control"
value="<?php echo $row['name']; ?>"
readonly>

</div>

<div class="mb-3">

<label>Order ID</label>

<input
type="text"
class="form-control"
value="#<?php echo $row['order_id']; ?>"
readonly>

</div>

<div class="mb-3">

<label>Amount</label>

<input
type="text"
class="form-control"
value="₹<?php echo number_format($row['amount'],2); ?>"
readonly>

</div>

<div class="mb-3">

<label>Payment Method</label>

<select
name="payment_method"
class="form-select"
required>

<option value="Cash on Delivery"
<?php if($row['payment_method']=="Cash on Delivery") echo "selected"; ?>>
Cash on Delivery
</option>

<option value="UPI"
<?php if($row['payment_method']=="UPI") echo "selected"; ?>>
UPI
</option>

<option value="Credit Card"
<?php if($row['payment_method']=="Credit Card") echo "selected"; ?>>
Credit Card
</option>

<option value="Debit Card"
<?php if($row['payment_method']=="Debit Card") echo "selected"; ?>>
Debit Card
</option>

<option value="Net Banking"
<?php if($row['payment_method']=="Net Banking") echo "selected"; ?>>
Net Banking
</option>

</select>

</div>

<div class="mb-3">

<label>Payment Status</label>

<select
name="payment_status"
class="form-select"
required>

<option value="Pending"
<?php if($row['payment_status']=="Pending") echo "selected"; ?>>
Pending
</option>

<option value="Paid"
<?php if($row['payment_status']=="Paid") echo "selected"; ?>>
Paid
</option>

</select>

</div>

<div class="mb-3">

<label>Transaction ID</label>

<input
type="text"
name="transaction_id"
class="form-control"
value="<?php echo $row['transaction_id']; ?>">

</div>

<div class="d-grid">

<button
type="submit"
name="update"
class="btn btn-success">

<i class="bi bi-save"></i>

Update Payment

</button>

</div>

</form>

<hr>

<a href="view.php" class="btn btn-secondary">

<i class="bi bi-arrow-left"></i>

Back to Payments

</a>

</div>

</div>

</div>

</div>

</div>

</body>

</html>