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

$id = (int)$_GET['id'];

$sql = "SELECT * FROM sweets WHERE sweet_id='$id'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    header("Location: view.php");
    exit();
}

$row = mysqli_fetch_assoc($result);
?>
<?php
$message = "";

if(isset($_POST['update']))
{
    $category_id = $_POST['category_id'];
    $sweet_name = trim($_POST['sweet_name']);
    $description = trim($_POST['description']);
    $price = $_POST['price'];
    $weight = trim($_POST['weight']);
    $stock_quantity = $_POST['stock_quantity'];
    $status = $_POST['status'];

    // Keep old image by default
    $imageName = $row['image'];

    // Check if a new image is uploaded
    if(isset($_FILES['image']) && $_FILES['image']['name'] != "")
    {
        $image = $_FILES['image']['name'];
        $temp = $_FILES['image']['tmp_name'];

        $imageName = time() . "_" . $image;

        $uploadPath = "../../uploads/sweets/" . $imageName;

        $allowed = array("jpg","jpeg","png","webp");

        $ext = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));

        if(in_array($ext, $allowed))
        {
            if(move_uploaded_file($temp, $uploadPath))
            {
                // Delete old image if it exists
                $oldImage = "../../uploads/sweets/" . $row['image'];

                if(file_exists($oldImage))
                {
                    unlink($oldImage);
                }
            }
            else
            {
                $message = "<div class='alert alert-danger'>
                Failed to upload the new image.
                </div>";
            }
        }
        else
        {
            $message = "<div class='alert alert-danger'>
            Only JPG, JPEG, PNG and WEBP images are allowed.
            </div>";
        }
    }

    if($message == "")
    {
        $sql = "UPDATE sweets SET
                category_id='$category_id',
                sweet_name='$sweet_name',
                description='$description',
                price='$price',
                weight='$weight',
                stock_quantity='$stock_quantity',
                image='$imageName',
                status='$status'
                WHERE sweet_id='$id'";

        if(mysqli_query($conn,$sql))
        {
            echo "<script>
                    alert('Sweet updated successfully.');
                    window.location='view.php';
                  </script>";
            exit();
        }
        else
        {
            $message = "<div class='alert alert-danger'>
            ".mysqli_error($conn)."
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

<title>Edit Sweet</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container mt-5">

<div class="card shadow">

<div class="card-header bg-warning">
<h3>Edit Sweet</h3>
</div>

<div class="card-body">

<form method="POST" enctype="multipart/form-data">

<div class="mb-3">

<label>Category</label>

<select name="category_id" class="form-control" required>

<?php

$catResult = mysqli_query($conn,"SELECT * FROM categories");

while($cat = mysqli_fetch_assoc($catResult))
{
?>

<option value="<?php echo $cat['category_id']; ?>"

<?php
if($cat['category_id'] == $row['category_id'])
echo "selected";
?>

>

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
value="<?php echo $row['sweet_name']; ?>"
required>

</div>

<div class="mb-3">

<label>Description</label>

<textarea
name="description"
class="form-control"
rows="4"
required><?php echo $row['description']; ?></textarea>

</div>

<div class="row">

<div class="col-md-4">

<label>Price</label>

<input
type="number"
step="0.01"
name="price"
class="form-control"
value="<?php echo $row['price']; ?>"
required>

</div>

<div class="col-md-4">

<label>Weight</label>

<input
type="text"
name="weight"
class="form-control"
value="<?php echo $row['weight']; ?>"
required>

</div>

<div class="col-md-4">

<label>Stock Quantity</label>

<input
type="number"
name="stock_quantity"
class="form-control"
value="<?php echo $row['stock_quantity']; ?>"
required>

</div>

</div>

<br>

<div class="mb-3">

<label>Current Image</label>

<br>

<img
src="../../uploads/sweets/<?php echo $row['image']; ?>"
width="120"
class="img-thumbnail">

</div>

<div class="mb-3">

<label>New Image (Optional)</label>

<input
type="file"
name="image"
class="form-control">

</div>

<div class="mb-3">

<label>Status</label>

<select
name="status"
class="form-control">

<option value="Yes"
<?php if($row['status']=="Yes") echo "selected"; ?>>
Available
</option>

<option value="No"
<?php if($row['status']=="No") echo "selected"; ?>>
Unavailable
</option>

</select>

</div>

<button
type="submit"
name="update"
class="btn btn-primary">

Update Sweet

</button>

<a href="view.php"
class="btn btn-secondary">

Back

</a>

</form>

</div>

</div>

</div>

</body>
</html>

