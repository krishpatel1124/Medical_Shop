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
// SEARCH
// ==========================================

$search = "";

if (isset($_GET['search'])) {

    $search = mysqli_real_escape_string(
        $conn,
        $_GET['search']
    );

}


// ==========================================
// PAGINATION
// ==========================================

$limit = 10;

$page = isset($_GET['page'])
    ? (int)$_GET['page']
    : 1;


if ($page < 1) {

    $page = 1;

}


$start = ($page - 1) * $limit;


// ==========================================
// TOTAL RECORDS
// ==========================================

$countSql = "

SELECT COUNT(DISTINCT s.sweet_id) AS total

FROM sweets s

INNER JOIN stock st

ON s.sweet_id = st.sweet_id

WHERE st.supplier_id = '$supplier_id'

";


if ($search != "") {

    $countSql .= "

    AND s.sweet_name LIKE '%$search%'

    ";

}


$countResult = mysqli_query(
    $conn,
    $countSql
);


if (!$countResult) {

    die(
        "Count Error: "
        . mysqli_error($conn)
    );

}


$totalRow = mysqli_fetch_assoc(
    $countResult
);


$totalPages = ceil(
    $totalRow['total'] / $limit
);


// ==========================================
// SWEET LIST
// ==========================================

$sql = "

SELECT

s.sweet_id,

s.sweet_name,

s.price,

s.weight,

s.image,

s.is_available,

c.category_name,

st.quantity_in_stock

FROM sweets s

INNER JOIN stock st

ON s.sweet_id = st.sweet_id

INNER JOIN category c

ON s.category_id = c.category_id

WHERE st.supplier_id = '$supplier_id'

";


if ($search != "") {

    $sql .= "

    AND s.sweet_name LIKE '%$search%'

    ";

}


$sql .= "

ORDER BY s.sweet_id DESC

LIMIT $start, $limit

";


$result = mysqli_query(
    $conn,
    $sql
);


if (!$result) {

    die(
        "SQL Error: "
        . mysqli_error($conn)
    );

}

?>


<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1">

<title>My Sweets</title>


<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">


<link
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
rel="stylesheet">


<style>

body {

    background: #f5f5f5;

}


.sidebar {

    width: 240px;

    height: 100vh;

    background: #198754;

    position: fixed;

    left: 0;

    top: 0;

}


.sidebar h3 {

    color: white;

    padding: 20px;

    text-align: center;

}


.sidebar a {

    display: block;

    padding: 15px 20px;

    color: white;

    text-decoration: none;

}


.sidebar a:hover {

    background: #157347;

}


.main {

    margin-left: 240px;

}


.topbar {

    background: white;

    padding: 15px 25px;

    box-shadow: 0 2px 10px rgba(0,0,0,.1);

}


.content {

    padding: 30px;

}


@media(max-width:768px) {

    .sidebar {

        position: relative;

        width: 100%;

        height: auto;

    }

    .main {

        margin-left: 0;

    }

}

</style>

</head>


<body>


<!-- ================= SIDEBAR ================= -->

<div class="sidebar">

<h3>

Supplier Panel

</h3>


<a href="../dashboard.php">

<i class="bi bi-speedometer2"></i>

Dashboard

</a>


<a href="../profile.php">

<i class="bi bi-person"></i>

Profile

</a>


<!-- CURRENT FOLDER -->

<a href="view.php">

<i class="bi bi-box"></i>

My Sweets

</a>


<a href="../stock/view.php">

<i class="bi bi-archive"></i>

Stock

</a>


<a href="../orders/view.php">

<i class="bi bi-bag"></i>

Orders

</a>


<a href="../change_password.php">

<i class="bi bi-key"></i>

Change Password

</a>


<a href="../logout.php">

<i class="bi bi-box-arrow-right"></i>

Logout

</a>


</div>


<!-- ================= MAIN ================= -->

<div class="main">


<!-- ================= TOPBAR ================= -->

<div class="topbar d-flex justify-content-between align-items-center">

<h3>

My Sweets

</h3>


<a
href="add.php"
class="btn btn-success">

<i class="bi bi-plus-circle"></i>

Add Sweet

</a>


</div>


<!-- ================= CONTENT ================= -->

<div class="content">


<!-- ================= SEARCH ================= -->

<form
method="GET"
class="row mb-3">


<div class="col-md-10">

<input
type="text"
name="search"
class="form-control"
placeholder="Search Sweet"
value="<?php echo htmlspecialchars($search); ?>">

</div>


<div class="col-md-2">

<button
type="submit"
class="btn btn-primary w-100">

Search

</button>

</div>


</form>


<!-- ================= SUCCESS MESSAGE ================= -->

<?php if (isset($_GET['success'])) { ?>

<div class="alert alert-success">

Sweet added successfully.

</div>

<?php } ?>


<?php if (isset($_GET['updated'])) { ?>

<div class="alert alert-success">

Sweet updated successfully.

</div>

<?php } ?>


<?php if (isset($_GET['deleted'])) { ?>

<div class="alert alert-success">

Sweet deleted successfully.

</div>

<?php } ?>


<!-- ================= TABLE ================= -->

<div class="card shadow">


<div class="card-body">


<div class="table-responsive">


<table class="table table-bordered table-hover">


<thead class="table-dark">


<tr>

<th>ID</th>

<th>Image</th>

<th>Name</th>

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


if (mysqli_num_rows($result) > 0) {


while ($row = mysqli_fetch_assoc($result)) {


?>


<tr>


<td>

<?php

echo $row['sweet_id'];

?>

</td>



<td>

<?php

if ($row['image'] != "") {

?>

<img
src="../../uploads/sweets/<?php echo htmlspecialchars($row['image']); ?>"
width="70"
height="70"
style="object-fit:cover;border-radius:8px;">

<?php

} else {

?>

No Image

<?php

}

?>

</td>



<td>

<?php

echo htmlspecialchars(
    $row['sweet_name']
);

?>

</td>



<td>

<?php

echo htmlspecialchars(
    $row['category_name']
);

?>

</td>



<td>

₹<?php

echo number_format(
    $row['price'],
    2
);

?>

</td>



<td>

<?php

echo htmlspecialchars(
    $row['weight']
);

?>

</td>



<td>

<?php

echo $row['quantity_in_stock'];

?>

</td>



<td>

<?php


if ($row['quantity_in_stock'] > 0) {

?>

<span class="badge bg-success">

Available

</span>

<?php

} else {

?>

<span class="badge bg-danger">

Out of Stock

</span>

<?php

}

?>

</td>



<td>


<a
href="edit.php?id=<?php echo $row['sweet_id']; ?>"
class="btn btn-warning btn-sm">

<i class="bi bi-pencil"></i>

Edit

</a>


<a
href="delete.php?id=<?php echo $row['sweet_id']; ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Delete this sweet?')">

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

<td
colspan="9"
class="text-center">

No sweets found.

</td>

</tr>


<?php

}

?>


</tbody>


</table>


</div>


<!-- ================= PAGINATION ================= -->

<?php if ($totalPages > 1) { ?>


<nav>


<ul class="pagination justify-content-center">


<?php

for (
    $i = 1;
    $i <= $totalPages;
    $i++
) {

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


<?php } ?>


</div>

</div>


</div>


</div>


</body>

</html>