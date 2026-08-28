<?php
session_start();
include("../../config/db.php");

if (!isset($_SESSION['supplier_id'])) {
    header("Location: ../../login.php");
    exit();
}

$supplier_id = $_SESSION['supplier_id'];

$message = "";

// Get Categories
$categories = mysqli_query($conn,"SELECT * FROM category ORDER BY category_name");

if(isset($_POST['save']))
{
    $category_id = (int)$_POST['category_id'];
    $sweet_name = $_POST['sweet_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $weight = $_POST['weight'];
    $status = $_POST['status'];

    $image = "";

    if($_FILES['image']['name']!="")
    {
        $image = time()."_".$_FILES['image']['name'];

        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            "../../uploads/sweets/".$image
        );
    }

    $sql = "
    INSERT INTO sweets
    (
        category_id,
        supplier_id,
        sweet_name,
        description,
        price,
        image,
        weight,
        status
    )
    VALUES
    (
        '$category_id',
        '$supplier_id',
        '$sweet_name',
        '$description',
        '$price',
        '$image',
        '$weight',
        '$status'
    )
    ";

    if(mysqli_query($conn,$sql))
    {
        header("Location:view.php?success=1");
        exit();
    }
    else
    {
        $message="<div class='alert alert-danger'>
        Failed to add sweet.
        </div>";
    }
}
?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Add Sweet</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-lg-8">

<div class="card shadow">

<div class="card-header bg-success text-white">

<h3>Add New Sweet</h3>

</div>

<div class="card-body">

<?php echo $message; ?>

<form method="POST" enctype="multipart/form-data">

<div class="mb-3">

<label>Category</label>

<select name="category_id" class="form-select" required>

<option value="">Select Category</option>

<?php
while($cat=mysqli_fetch_assoc($categories))
{
?>

<option value="<?php echo $cat['category_id']; ?>">

<?php echo $cat['category_name']; ?>

</option>

<?php
}
?>

</select>

</div>

<div class="mb-3">

<label>Sweet Name</label>

<input
type="text"
name="sweet_name"
class="form-control"
required>

</div>

<div class="mb-3">

<label>Description</label>

<textarea
name="description"
class="form-control"
rows="4"></textarea>

</div>

<div class="row">

<div class="col-md-6">

<div class="mb-3">

<label>Price</label>

<input
type="number"
step="0.01"
name="price"
class="form-control"
required>

</div>

</div>

<div class="col-md-6">

<div class="mb-3">

<label>Weight</label>

<input
type="text"
name="weight"
class="form-control"
placeholder="500 g / 1 kg"
required>

</div>

</div>

</div>

<div class="mb-3">

<label>Image</label>

<input
type="file"
name="image"
class="form-control"
accept="image/*"
required>

</div>

<div class="mb-3">

<label>Status</label>

<select
name="status"
class="form-select">

<option value="Yes">Available</option>

<option value="No">Unavailable</option>

</select>

</div>

<div class="d-grid">

<button
type="submit"
name="save"
class="btn btn-success">

Add Sweet

</button>

</div>

</form>

<hr>

<a href="view.php" class="btn btn-secondary">

Back to My Sweets

</a>

</div>

</div>

</div>

</div>

</div>

</body>

</html>