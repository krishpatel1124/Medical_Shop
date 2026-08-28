<?php

session_start();

include("../../config/db.php");


// ==========================================
// SUPPLIER LOGIN CHECK
// ==========================================

if (!isset($_SESSION['supplier_id'])) {

    header("Location: ../../login.php");
    exit();

}


$supplier_id = $_SESSION['supplier_id'];


// ==========================================
// STOCK ID CHECK
// ==========================================

if (!isset($_GET['id'])) {

    header("Location: view.php");
    exit();

}


$stock_id = (int)$_GET['id'];

$message = "";


// ==========================================
// UPDATE STOCK
// ==========================================

if (isset($_POST['update'])) {

    $quantity_in_stock = $_POST['quantity_in_stock'];

    $updateQuery = mysqli_query($conn, "

        UPDATE stock

        SET
            quantity_in_stock = '$quantity_in_stock',
            last_updated = NOW()

        WHERE
            stock_id = '$stock_id'

        AND
            supplier_id = '$supplier_id'

    ");


    if ($updateQuery) {

        $message = "

        <div class='alert alert-success'>

            Stock updated successfully!

        </div>

        ";

    } else {

        $message = "

        <div class='alert alert-danger'>

            Error: " . mysqli_error($conn) . "

        </div>

        ";

    }

}


// ==========================================
// GET STOCK RECORD
// ==========================================

$stockQuery = mysqli_query($conn, "

    SELECT

        st.stock_id,
        st.sweet_id,
        st.supplier_id,
        st.quantity_in_stock,
        st.reorder_level,
        st.last_updated,

        s.sweet_name,
        s.price,
        s.weight,
        s.image

    FROM stock st

    INNER JOIN sweets s

        ON st.sweet_id = s.sweet_id

    WHERE
        st.stock_id = '$stock_id'

    AND
        st.supplier_id = '$supplier_id'

");


if (!$stockQuery) {

    die("SQL Error: " . mysqli_error($conn));

}


if (mysqli_num_rows($stockQuery) == 0) {

    die("Stock record not found.");

}


$stock = mysqli_fetch_assoc($stockQuery);

?>


<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1">

<title>Update Stock</title>


<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">


<link
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
rel="stylesheet">

</head>


<body class="bg-light">


<div class="container mt-5">


<div class="row justify-content-center">


<div class="col-lg-6">


<div class="card shadow">


<!-- HEADER -->

<div class="card-header bg-warning">

<h3 class="mb-0">

Update Stock

</h3>

</div>



<div class="card-body">


<!-- MESSAGE -->

<?php echo $message; ?>


<!-- SWEET IMAGE -->

<div class="text-center mb-3">


<img

src="../../uploads/sweets/<?php echo htmlspecialchars($stock['image']); ?>"

width="120"

class="img-thumbnail"

alt="Sweet Image"


>


<h4 class="mt-3">


<?php

echo htmlspecialchars(
    $stock['sweet_name']
);

?>


</h4>


</div>



<!-- UPDATE FORM -->

<form method="POST">


<div class="mb-3">


<label class="form-label">

Current Stock Quantity

</label>


<input

type="number"

name="quantity_in_stock"

class="form-control"

value="<?php echo $stock['quantity_in_stock']; ?>"

required


>


</div>



<!-- LAST UPDATED -->

<div class="mb-3">


<label class="form-label">

Last Updated

</label>


<input

type="text"

class="form-control"

value="<?php echo $stock['last_updated']; ?>"

readonly


>


</div>



<div class="d-grid">


<button

type="submit"

name="update"

class="btn btn-warning">


<i class="bi bi-save"></i>

Update Stock


</button>


</div>


</form>



<hr>



<a
href="view.php"
class="btn btn-secondary">


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