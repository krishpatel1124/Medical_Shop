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
// GET SWEET ID
// ==========================================

if (!isset($_GET['id'])) {

    header("Location: view.php");
    exit();

}

$sweet_id = (int)$_GET['id'];


// ==========================================
// GET SWEET DETAILS
// ==========================================

$query = mysqli_query($conn, "

    SELECT s.*

    FROM sweets s

    INNER JOIN stock st

        ON s.sweet_id = st.sweet_id

    WHERE s.sweet_id = '$sweet_id'

    AND st.supplier_id = '$supplier_id'

");


if (!$query) {

    die("SQL Error: " . mysqli_error($conn));

}


if (mysqli_num_rows($query) == 0) {

    die("Sweet not available.");

}


$sweet = mysqli_fetch_assoc($query);


// ==========================================
// UPDATE SWEET
// ==========================================

if (isset($_POST['update'])) {

    $sweet_name = mysqli_real_escape_string(
        $conn,
        $_POST['sweet_name']
    );

    $description = mysqli_real_escape_string(
        $conn,
        $_POST['description']
    );

    $price = $_POST['price'];

    $weight = mysqli_real_escape_string(
        $conn,
        $_POST['weight']
    );

    $is_available = $_POST['is_available'];


    $updateQuery = mysqli_query($conn, "

        UPDATE sweets

        SET

            sweet_name = '$sweet_name',

            description = '$description',

            price = '$price',

            weight = '$weight',

            is_available = '$is_available'

        WHERE sweet_id = '$sweet_id'

    ");


    if ($updateQuery) {

        header("Location: view.php?updated=1");
        exit();

    } else {

        echo "Update Error: " . mysqli_error($conn);

    }

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Update Sweet</title>

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


<div class="col-md-7">


<div class="card shadow">


<div class="card-header bg-warning">

<h3 class="mb-0">

Update Sweet

</h3>

</div>


<div class="card-body">


<!-- SWEET IMAGE -->

<div class="text-center mb-4">

<?php if (!empty($sweet['image'])) { ?>

<img
src="../../uploads/sweets/<?php echo htmlspecialchars($sweet['image']); ?>"
width="150"
class="img-thumbnail"
alt="Sweet Image">

<?php } ?>


<h4 class="mt-3">

<?php echo htmlspecialchars($sweet['sweet_name']); ?>

</h4>

</div>


<!-- UPDATE FORM -->

<form method="POST">


<!-- SWEET NAME -->

<div class="mb-3">

<label class="form-label">

Sweet Name

</label>

<input
type="text"
name="sweet_name"
class="form-control"
value="<?php echo htmlspecialchars($sweet['sweet_name']); ?>"
required>

</div>


<!-- DESCRIPTION -->

<div class="mb-3">

<label class="form-label">

Description

</label>

<textarea
name="description"
class="form-control"
rows="3"
required><?php echo htmlspecialchars($sweet['description']); ?></textarea>

</div>


<!-- PRICE -->

<div class="mb-3">

<label class="form-label">

Price

</label>

<input
type="number"
name="price"
class="form-control"
value="<?php echo $sweet['price']; ?>"
step="0.01"
required>

</div>


<!-- WEIGHT -->

<div class="mb-3">

<label class="form-label">

Weight

</label>

<input
type="text"
name="weight"
class="form-control"
value="<?php echo htmlspecialchars($sweet['weight']); ?>"
required>

</div>


<!-- STATUS -->

<div class="mb-3">

<label class="form-label">

Availability

</label>

<select
name="is_available"
class="form-select">

<option
value="Yes"
<?php if ($sweet['is_available'] == "Yes") echo "selected"; ?>>

Available

</option>


<option
value="No"
<?php if ($sweet['is_available'] == "No") echo "selected"; ?>>

Not Available

</option>

</select>

</div>


<!-- BUTTON -->

<div class="d-grid">

<button
type="submit"
name="update"
class="btn btn-warning">

<i class="bi bi-save"></i>

Update Sweet

</button>

</div>


</form>


<hr>


<a
href="view.php"
class="btn btn-secondary">

<i class="bi bi-arrow-left"></i>

Back to My Sweets

</a>


</div>

</div>


</div>


</div>


</div>


</body>

</html>