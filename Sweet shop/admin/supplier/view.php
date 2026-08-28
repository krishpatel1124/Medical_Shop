<?php
session_start();
include("../../config/db.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

$search = "";

if(isset($_GET['search']))
{
    $search = mysqli_real_escape_string($conn,$_GET['search']);
}

$limit = 10;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if($page < 1)
$page = 1;

$start = ($page-1) * $limit;

/* Total Records */

$count = "SELECT COUNT(*) AS total
FROM supplier_detail";

if($search!="")
{
    $count .= " WHERE supplier_name LIKE '%$search%'
                OR email LIKE '%$search%'
                OR mobile_no LIKE '%$search%'";
}

$countResult = mysqli_query($conn,$count);

$total = mysqli_fetch_assoc($countResult);

$totalPages = ceil($total['total']/$limit);

/* Supplier List */

$sql = "SELECT *
        FROM supplier_detail";

if($search!="")
{
    $sql .= " WHERE supplier_name LIKE '%$search%'
              OR email LIKE '%$search%'
              OR mobile_no LIKE '%$search%'";
}

$sql .= " ORDER BY supplier_id DESC
          LIMIT $start,$limit";

$result = mysqli_query($conn,$sql);

?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Supplier Management</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-4">

<div class="d-flex justify-content-between align-items-center mb-3">

<h2>

<i class="bi bi-truck"></i>

Supplier Management

</h2>

<a href="add.php" class="btn btn-success">

<i class="bi bi-plus-circle"></i>

Add Supplier

</a>

</div>

<form method="GET" class="row mb-3">

<div class="col-md-10">

<input
type="text"
name="search"
class="form-control"
placeholder="Search Supplier"
value="<?php echo htmlspecialchars($search); ?>">

</div>

<div class="col-md-2">

<button class="btn btn-primary w-100">

Search

</button>

</div>

</form>

<?php

if(isset($_GET['success']))
{
    echo "<div class='alert alert-success'>Supplier added successfully.</div>";
}

if(isset($_GET['updated']))
{
    echo "<div class='alert alert-success'>Supplier updated successfully.</div>";
}

if(isset($_GET['deleted']))
{
    echo "<div class='alert alert-success'>Supplier deleted successfully.</div>";
}

?>

<div class="card shadow">

<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead class="table-dark">

<tr>

<th>ID</th>

<th>Supplier Name</th>

<th>Email</th>

<th>Mobile</th>

<th>Address</th>

<th>Action</th>

</tr>

</thead>

<tbody>

<?php

if(mysqli_num_rows($result)>0)
{
    while($row=mysqli_fetch_assoc($result))
    {
?>

<tr>

<td><?php echo $row['supplier_id']; ?></td>

<td><?php echo htmlspecialchars($row['supplier_name']); ?></td>

<td><?php echo htmlspecialchars($row['email']); ?></td>

<td><?php echo htmlspecialchars($row['mobile_no']); ?></td>

<td><?php echo htmlspecialchars($row['address']); ?></td>

<td>

<a href="edit.php?id=<?php echo $row['supplier_id']; ?>"
class="btn btn-warning btn-sm">

<i class="bi bi-pencil-square"></i>

</a>

<a href="delete.php?id=<?php echo $row['supplier_id']; ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Delete this supplier?')">

<i class="bi bi-trash"></i>

</a>

</td>

</tr>

<?php

    }
}
else
{
?>

<tr>

<td colspan="6" class="text-center">

No Suppliers Found

</td>

</tr>

<?php

}

?>

</tbody>

</table>

</div>

<nav>

<ul class="pagination justify-content-center">

<?php

for($i=1;$i<=$totalPages;$i++)
{

?>

<li class="page-item <?php if($page==$i) echo 'active'; ?>">

<a class="page-link"
href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>">

<?php echo $i; ?>

</a>

</li>

<?php

}

?>

</ul>

</nav>

<a href="../dashboard.php"
class="btn btn-secondary">

<i class="bi bi-arrow-left"></i>

Back to Dashboard

</a>

</div>

</div>

</div>

</body>

</html>