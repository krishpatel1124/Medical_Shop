<?php

session_start();
include("../../config/db.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}


// ==========================================
// SEARCH
// ==========================================

$search = "";

if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
}


// ==========================================
// PAGINATION
// ==========================================

$limit = 10;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

$start = ($page - 1) * $limit;


// ==========================================
// COUNT SWEETS
// ==========================================

$count = "
    SELECT COUNT(*) AS total
    FROM sweets
";

if ($search != "") {

    $count .= "
        WHERE sweet_name LIKE '%$search%'
    ";
}

$countResult = mysqli_query($conn, $count);

$total = mysqli_fetch_assoc($countResult);

$totalPages = ceil($total['total'] / $limit);


// ==========================================
// GET SWEETS
// ==========================================

$sql = "
    SELECT
        sweet_id,
        sweet_name,
        image,
        quantity,
        is_available
    FROM sweets
";

if ($search != "") {

    $sql .= "
        WHERE sweet_name LIKE '%$search%'
    ";
}

$sql .= "
    ORDER BY sweet_id DESC
    LIMIT $start, $limit
";

$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>Stock Management</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
rel="stylesheet">

</head>


<body class="bg-light">


<div class="container mt-4">


<!-- HEADER -->

<div class="d-flex justify-content-between align-items-center mb-3">

<h2>

<i class="bi bi-box-seam"></i>

Stock Management

</h2>

</div>


<!-- SEARCH -->

<form method="GET" class="row mb-3">

<div class="col-md-10">

<input
type="text"
name="search"
class="form-control"
placeholder="Search Sweet"
value="<?php echo htmlspecialchars($search); ?>">

</div>


<div class="col-md-2">

<button class="btn btn-primary w-100">

<i class="bi bi-search"></i>

Search

</button>

</div>

</form>


<!-- TABLE -->

<div class="card shadow">

<div class="card-body">

<div class="table-responsive">


<table class="table table-bordered table-hover">


<thead class="table-dark">

<tr>

<th>ID</th>

<th>Image</th>

<th>Sweet</th>

<th>Quantity</th>

<th>Status</th>

<th>Action</th>

</tr>

</thead>


<tbody>


<?php

if (mysqli_num_rows($result) > 0) {

    while ($row = mysqli_fetch_assoc($result)) {

?>


<tr>


<!-- ID -->

<td>

<?php echo $row['sweet_id']; ?>

</td>


<!-- IMAGE -->

<td>

<img
src="../../uploads/sweets/<?php echo htmlspecialchars($row['image']); ?>"
width="70"
height="70"
style="object-fit:cover;border-radius:8px;">

</td>


<!-- SWEET NAME -->

<td>

<?php echo htmlspecialchars($row['sweet_name']); ?>

</td>


<!-- QUANTITY -->

<td>

<?php echo $row['quantity']; ?>

</td>


<!-- STATUS -->

<td>

<?php

if ($row['is_available'] == "yes") {

    if ($row['quantity'] <= 10) {

        echo "<span class='badge bg-danger'>Low Stock</span>";

    } elseif ($row['quantity'] <= 30) {

        echo "<span class='badge bg-warning text-dark'>Medium Stock</span>";

    } else {

        echo "<span class='badge bg-success'>In Stock</span>";

    }

} else {

    echo "<span class='badge bg-danger'>Unavailable</span>";

}

?>

</td>


<!-- ACTION -->

<td>

<a
href="../sweets/edit.php?id=<?php echo $row['sweet_id']; ?>"
class="btn btn-warning btn-sm">

<i class="bi bi-pencil-square"></i>

Edit

</a>


<a
href="../sweets/delete.php?id=<?php echo $row['sweet_id']; ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Are you sure you want to delete this sweet?');">

<i class="bi bi-trash"></i>

Delete

</a>

</td>


</tr>


<?php

    }

} else {

?>


<tr>

<td colspan="6" class="text-center">

No Stock Found

</td>

</tr>


<?php

}

?>


</tbody>

</table>

</div>


<!-- PAGINATION -->

<nav>

<ul class="pagination justify-content-center">


<?php

for ($i = 1; $i <= $totalPages; $i++) {

?>


<li class="page-item <?php if ($page == $i) echo 'active'; ?>">


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


<!-- BACK -->

<a
href="../dashboard.php"
class="btn btn-secondary">

<i class="bi bi-arrow-left"></i>

Back to Dashboard

</a>


</div>

</div>

</div>


</body>

</html>