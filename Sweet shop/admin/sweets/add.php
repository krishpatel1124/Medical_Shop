<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include("../../config/db.php");

$message = "";

if(isset($_POST['save']))
{
    $category_id = $_POST['category_id'];
    $sweet_name = trim($_POST['sweet_name']);
    $description = trim($_POST['description']);
    $price = $_POST['price'];
    $weight = trim($_POST['weight']);
    $stock_quantity = $_POST['stock_quantity'];
    $status = $_POST['status'];

    // Image Upload
    $image = $_FILES['image']['name'];
    $temp = $_FILES['image']['tmp_name'];

    // Generate unique filename
    $imageName = time() . "_" . $image;

    // Upload folder
    $uploadPath = "../../uploads/sweets/" . $imageName;

    // Allowed extensions
    $allowed = array("jpg","jpeg","png","webp");

    $ext = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));

    if(!in_array($ext, $allowed))
    {
        $message = "<div class='alert alert-danger'>
        Only JPG, JPEG, PNG and WEBP images are allowed.
        </div>";
    }
    else
    {
        if(move_uploaded_file($temp, $uploadPath))
        {
            $sql = "INSERT INTO sweets
            (category_id,sweet_name,description,price,weight,stock_quantity,image,status)
            VALUES
            ('$category_id','$sweet_name','$description','$price','$weight',
            '$stock_quantity','$imageName','$status')";

            if(mysqli_query($conn,$sql))
            {
                $message = "<div class='alert alert-success'>
                Sweet added successfully.
                </div>";
            }
            else
            {
                $message = "<div class='alert alert-danger'>
                Database error: ".mysqli_error($conn)."
                </div>";
            }
        }
        else
        {
            $message = "<div class='alert alert-danger'>
            Image upload failed.
            </div>";
        }
    }
}

if(!isset($_SESSION['admin_id']))
{
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Add Sweet</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container mt-5">

<div class="card shadow">

<div class="card-header bg-success text-white">

<h3>Add New Sweet</h3>

</div>

<div class="card-body">

<?php echo $message; ?>

<form action="" method="POST" enctype="multipart/form-data">

<div class="row">

<div class="col-md-6 mb-3">

<label>Category</label>

<select name="category_id" class="form-control" required>

<option value="">Select Category</option>

<?php

$result=mysqli_query($conn,"SELECT * FROM category");

while($cat=mysqli_fetch_assoc($result))
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

<div class="col-md-6 mb-3">

<label>Sweet Name</label>

<input type="text"
name="sweet_name"
class="form-control"
required>

</div>

</div>

<div class="mb-3">

<label>Description</label>

<textarea
name="description"
class="form-control"
rows="4"
required></textarea>

</div>

<div class="row">

<div class="col-md-4 mb-3">

<label>Price (₹)</label>

<input type="number"
name="price"
step="0.01"
class="form-control"
required>

</div>

<div class="col-md-4 mb-3">

<label>Weight</label>

<input type="text"
name="weight"
class="form-control"
placeholder="500 g / 1 kg"
required>

</div>

<div class="col-md-4 mb-3">

<label>Stock Quantity</label>

<input type="number"
name="stock_quantity"
class="form-control"
required>

</div>

</div>

<div class="mb-3">

<label>Sweet Image</label>

<input type="file"
name="image"
class="form-control"
accept="image/*"
required>

</div>

<div class="mb-3">

<label>Status</label>

<select
name="status"
class="form-control">

<option value="Yes">Available</option>

<option value="No">Unavailable</option>

</select>

</div>

<button
type="submit"
name="save"
class="btn btn-success">

Save Sweet

</button>

<a href="view.php"
class="btn btn-secondary">

View Sweets

</a>

</form>

</div>

</div>

</div>

</body>
</html>