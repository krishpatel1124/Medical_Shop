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

$category_id = (int)$_GET['id'];

$result = mysqli_query($conn,
"SELECT * FROM category
WHERE category_id='$category_id'");

if(mysqli_num_rows($result)==0)
{
    header("Location:view.php");
    exit();
}

$row = mysqli_fetch_assoc($result);

$message = "";

if(isset($_POST['update']))
{
    $category_name = mysqli_real_escape_string($conn, trim($_POST['category_name']));
    $description = mysqli_real_escape_string($conn, trim($_POST['description']));

    // Check duplicate category name
    $check = mysqli_query($conn,
    "SELECT * FROM category
     WHERE category_name='$category_name'
     AND category_id!='$category_id'");

    if(mysqli_num_rows($check)>0)
    {
        $message = "<div class='alert alert-danger'>
        Category already exists.
        </div>";
    }
    else
    {
        $sql = "UPDATE category SET
                category_name='$category_name',
                description='$description'
                WHERE category_id='$category_id'";

        if(mysqli_query($conn,$sql))
        {
            header("Location:view.php?updated=1");
            exit();
        }
        else
        {
            $message = "<div class='alert alert-danger'>
            Failed to update category.
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

<title>Edit Category</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-md-7">

<div class="card shadow">

<div class="card-header bg-warning">

<h3>

<i class="bi bi-pencil-square"></i>

Edit Category

</h3>

</div>

<div class="card-body">

<?php echo $message; ?>

<form method="POST">

<div class="mb-3">

<label class="form-label">

Category Name

</label>

<input
type="text"
name="category_name"
class="form-control"
value="<?php echo htmlspecialchars($row['category_name']); ?>"
required>

</div>

<div class="mb-3">

<label class="form-label">

Description

</label>

<textarea
name="description"
class="form-control"
rows="4"><?php echo htmlspecialchars($row['description']); ?></textarea>

</div>

<div class="d-grid">

<button
type="submit"
name="update"
class="btn btn-warning">

<i class="bi bi-save"></i>

Update Category

</button>

</div>

</form>

<hr>

<a href="view.php" class="btn btn-secondary">

<i class="bi bi-arrow-left"></i>

Back to Categories

</a>

</div>

</div>

</div>

</div>

</div>

</body>

</html>