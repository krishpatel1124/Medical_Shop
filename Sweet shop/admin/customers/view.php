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

if($page<1) $page=1;

$start = ($page-1)*$limit;

$countSql = "SELECT COUNT(*) AS total
             FROM customer_detail";

if($search!="")
{
    $countSql .= " WHERE name LIKE '%$search%'
                   OR email LIKE '%$search%'
                   OR mobile_no LIKE '%$search%'";
}

$countResult = mysqli_query($conn,$countSql);
$countRow = mysqli_fetch_assoc($countResult);

$totalRecords = $countRow['total'];
$totalPages = ceil($totalRecords/$limit);

$sql = "SELECT *
        FROM customer_detail";

if($search!="")
{
    $sql .= " WHERE name LIKE '%$search%'
              OR email LIKE '%$search%'
              OR mobile_no LIKE '%$search%'";
}

$sql .= " ORDER BY customer_id DESC
          LIMIT $start,$limit";

$result = mysqli_query($conn,$sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Customers</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-4">

<div class="d-flex justify-content-between align-items-center mb-3">

<h2>Customers</h2>

<a href="add.php" class="btn btn-success">

<i class="bi bi-plus-circle"></i>

Add Customer

</a>

</div>

<form method="GET" class="row mb-3">

<div class="col-md-10">

<input
type="text"
name="search"
class="form-control"
placeholder="Search by Name, Email or Mobile"
value="<?php echo htmlspecialchars($search); ?>">

</div>

<div class="col-md-2">

<button class="btn btn-primary w-100">

Search

</button>

</div>

</form>

<div class="card shadow">

<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead class="table-dark">

<tr>

<th>ID</th>

<th>Name</th>

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

<td><?php echo $row['customer_id']; ?></td>

<td><?php echo $row['name']; ?></td>

<td><?php echo $row['email']; ?></td>

<td><?php echo $row['mobile_no']; ?></td>

<td><?php echo $row['address']; ?></td>

<td>

<a
href="edit.php?id=<?php echo $row['customer_id']; ?>"
class="btn btn-warning btn-sm">

<i class="bi bi-pencil-square"></i>

Edit

</a>

<a
href="delete.php?id=<?php echo $row['customer_id']; ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Delete this customer?');">

<i class="bi bi-trash"></i>

Delete

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

No Customers Found

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

<li class="page-item <?php if($i==$page) echo 'active'; ?>">

<a
class="page-link"
href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>">

<?php echo $i; ?>

</a>

</li>

<?php
}
?>

</ul>

</nav>

</div>

</div>

<a href="../dashboard.php" class="btn btn-secondary mt-3">

<i class="bi bi-arrow-left"></i>

Back to Dashboard

</a>

</div>

</body>

</html>