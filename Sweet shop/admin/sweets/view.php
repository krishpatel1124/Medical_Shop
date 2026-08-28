<?php
session_start();
include("../../config/db.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Manage Sweets</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container mt-5">

<div class="d-flex justify-content-between align-items-center mb-4">

<h2>Manage Sweets</h2>

<form method="GET" class="mb-3">

<div class="row">

<div class="col-md-6">

<input
type="text"
name="search"
class="form-control"
placeholder="Search by Sweet Name or Category"
value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">

</div>

<div class="col-md-2">

<button
type="submit"
class="btn btn-primary w-110">

Search

</button>
</t>

</div>

<div class="col-md-2">

<a href="view.php"
class="btn btn-secondary ">

Reset

</a>

</div>

</div>

</form>



<div>
<a href="../dashboard.php" class="btn btn-secondary">
Dashboard
</a>

<a href="add.php" class="btn btn-success">
Add Sweet
</a>
</div>

</div>

<div class="card shadow">

<div class="card-header bg-primary text-white">
<h4 class="mb-0">Sweet List</h4>
</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered table-hover align-middle">

<thead class="table-dark">

<tr>

<th>ID</th>
<th>Image</th>
<th>Sweet Name</th>
<th>Category</th>
<th>Price</th>
<th>Weight</th>
<th>Stock</th>
<th>Status</th>
<th>Action</th>

</tr>

</thead>

<tbody>

<?php

$limit = 10;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

$start = ($page - 1) * $limit;

$search = "";

if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
}


// ==========================================
// COUNT SWEETS
// ==========================================

$countQuery = "
    SELECT COUNT(*) AS total
    FROM sweets
    INNER JOIN category
        ON sweets.category_id = category.category_id
";

if ($search != "") {

    $countQuery .= "
        WHERE sweets.sweet_name LIKE '%$search%'
        OR category.category_name LIKE '%$search%'
    ";

}

$countResult = mysqli_query($conn, $countQuery);

$totalRows = mysqli_fetch_assoc($countResult)['total'];

$totalPages = ceil($totalRows / $limit);


// ==========================================
// FETCH SWEETS
// ==========================================

$sql = "
    SELECT
        sweets.*,
        category.category_name
    FROM sweets

    INNER JOIN category
        ON sweets.category_id = category.category_id
";

if ($search != "") {

    $sql .= "
        WHERE sweets.sweet_name LIKE '%$search%'
        OR category.category_name LIKE '%$search%'
    ";

}

$sql .= "
    ORDER BY sweets.sweet_id DESC
    LIMIT $start, $limit
";

$result = mysqli_query($conn, $sql);


// ==========================================
// DISPLAY SWEETS
// ==========================================

if (mysqli_num_rows($result) > 0) {

    while ($row = mysqli_fetch_assoc($result)) {

?>

<tr>

    <!-- SWEET ID -->
    <td>
        <?php echo $row['sweet_id']; ?>
    </td>


    <!-- IMAGE -->
    <td>

        <img
            src="../../uploads/sweets/<?php echo htmlspecialchars($row['image']); ?>"
            width="80"
            height="80"
            style="object-fit:cover;border-radius:8px;"
        >

    </td>


    <!-- SWEET NAME -->
    <td>
        <?php echo htmlspecialchars($row['sweet_name']); ?>
    </td>


    <!-- CATEGORY -->
    <td>
        <?php echo htmlspecialchars($row['category_name']); ?>
    </td>


    <!-- PRICE -->
    <td>
        ₹<?php echo $row['price']; ?>
    </td>


    <!-- WEIGHT -->
    <td>
        <?php echo htmlspecialchars($row['weight']); ?>
    </td>


    <!-- QUANTITY -->
    <td>
        <?php echo $row['quantity']; ?>
    </td>


    <!-- AVAILABILITY -->
    <td>

       <td>
    <?php if ($row['is_available'] == "yes") { ?>
        <span class="badge bg-success">Available</span>
    <?php } else { ?>
        <span class="badge bg-danger">Unavailable</span>
    <?php } ?>
</td>

    </td>


    <!-- ACTIONS -->
    <td>

        <a
            href="edit.php?id=<?php echo $row['sweet_id']; ?>"
            class="btn btn-warning btn-sm"
        >
            Edit
        </a>


        <a
            href="delete.php?id=<?php echo $row['sweet_id']; ?>"
            class="btn btn-danger btn-sm"
            onclick="return confirm('Are you sure you want to delete this sweet?');"
        >
            Delete
        </a>

    </td>

</tr>

<?php

    }

} else {

?>

<tr>

    <td colspan="9" class="text-center">
        No sweets found.
    </td>

</tr>

<?php

}

?>

</tbody>

</table>

<nav class="mt-4">

<ul class="pagination justify-content-center">

<?php

for($i = 1; $i <= $totalPages; $i++)
{

?>

<li class="page-item <?php if($i == $page) echo 'active'; ?>">

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



</div>

</div>

</div>

</div>

</body>
</html>

