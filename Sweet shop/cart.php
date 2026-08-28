<?php

session_start();

include("config/db.php");

// ==========================================
// CHECK CUSTOMER LOGIN
// ==========================================

if (!isset($_SESSION['customer_id'])) {

    header("Location: login.php");
    exit();

}

$customer_id = (int)$_SESSION['customer_id'];


// ==========================================
// GET CART ITEMS
// ==========================================

$query = mysqli_query($conn, "

    SELECT
        cart.cart_id,
        cart.sweet_id,
        cart.weight,
        cart.price,
        cart.quantity,

        sweets.sweet_name,
        sweets.image,
        sweets.description

    FROM cart

    INNER JOIN sweets
        ON cart.sweet_id = sweets.sweet_id

    WHERE cart.customer_id = '$customer_id'

    ORDER BY cart.cart_id DESC

");


if (!$query) {

    die(
        "Cart Error: " .
        mysqli_error($conn)
    );

}


// ==========================================
// TOTAL
// ==========================================

$grandTotal = 0;

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1"
>

<title>My Cart - Sweet Shop</title>


<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet"
>


<link
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
rel="stylesheet"
>


<style>

body{

    background:#f8f9fa;

}


.navbar-brand{

    font-weight:bold;

}


.cart-card{

    border:none;

    border-radius:12px;

    box-shadow:
        0 3px 12px
        rgba(0,0,0,0.08);

}


.cart-image{

    width:100px;

    height:100px;

    object-fit:cover;

    border-radius:10px;

}


.price{

    color:#198754;

    font-weight:bold;

}


.weight{

    color:#555;

}


.quantity-box{

    display:flex;

    align-items:center;

    gap:8px;

}


.quantity-box .btn{

    width:35px;

    height:35px;

    padding:0;

}


.total-box{

    background:white;

    border-radius:12px;

    padding:25px;

    box-shadow:
        0 3px 12px
        rgba(0,0,0,0.08);

}


.empty-cart{

    background:white;

    padding:60px 20px;

    border-radius:12px;

    text-align:center;

}


</style>

</head>


<body>


<!-- ==========================================
     NAVBAR
========================================== -->

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">

<div class="container">


<a
class="navbar-brand"
href="index.php">

Sweet Shop

</a>


<button
class="navbar-toggler"
type="button"
data-bs-toggle="collapse"
data-bs-target="#menu"
>

<span class="navbar-toggler-icon"></span>

</button>


<div
class="collapse navbar-collapse"
id="menu"
>


<ul class="navbar-nav ms-auto">


<li class="nav-item">

<a
class="nav-link"
href="index.php">

Home

</a>

</li>


<li class="nav-item">

<a
class="nav-link"
href="sweets.php">

Sweets

</a>

</li>


<li class="nav-item">

<a
class="nav-link active"
href="cart.php">

<i class="bi bi-cart"></i>

Cart

</a>

</li>


<li class="nav-item">

<a
class="nav-link"
href="about.php">

About

</a>

</li>


<li class="nav-item">

<a
class="nav-link"
href="contact.php">

Contact

</a>

</li>


</ul>


</div>

</div>

</nav>



<!-- ==========================================
     CART
========================================== -->

<div class="container py-5">


<h2 class="mb-4">

<i class="bi bi-cart3"></i>

My Shopping Cart

</h2>


<?php

if (mysqli_num_rows($query) == 0) {

?>


<!-- EMPTY CART -->

<div class="empty-cart shadow-sm">


<i
class="bi bi-cart-x"
style="font-size:60px;color:#6c757d;"
></i>


<h3 class="mt-3">

Your cart is empty

</h3>


<p class="text-muted">

You haven't added any sweets yet.

</p>


<a
href="sweets.php"
class="btn btn-warning"
>

Continue Shopping

</a>


</div>


<?php

}

else {

?>


<div class="row">


<!-- ==========================================
     CART ITEMS
========================================== -->

<div class="col-lg-8">


<?php

while ($row = mysqli_fetch_assoc($query)) {


    $itemPrice =
        (float)$row['price'];


    $quantity =
        (int)$row['quantity'];


    $itemTotal =
        $itemPrice * $quantity;


    $grandTotal += $itemTotal;


    // Format weight

    $weight =
        (int)$row['weight'];


    if ($weight >= 1000) {

        if ($weight % 1000 == 0) {

            $weightDisplay =
                ($weight / 1000) . " kg";

        }

        else {

            $weightDisplay =
                number_format(
                    $weight / 1000,
                    2
                ) . " kg";

        }

    }

    else {

        $weightDisplay =
            $weight . " g";

    }

?>


<!-- CART CARD -->

<div
class="card cart-card mb-3"
>


<div class="card-body">


<div class="row align-items-center">


<!-- IMAGE -->

<div class="col-md-2 text-center">


<img
src="uploads/sweets/<?php
echo htmlspecialchars($row['image']);
?>"
class="cart-image"
alt="<?php
echo htmlspecialchars($row['sweet_name']);
?>"
>


</div>


<!-- SWEET INFORMATION -->

<div class="col-md-3">


<h5 class="mb-1">

<?php

echo htmlspecialchars(
    $row['sweet_name']
);

?>

</h5>


<div class="weight">

<strong>Weight:</strong>

<?php

echo $weightDisplay;

?>

</div>


<div class="price">

₹<?php

echo number_format(
    $itemPrice,
    2
);

?>

</div>


</div>


<!-- QUANTITY -->

<div class="col-md-3">


<label class="form-label">

Quantity

</label>


<div class="quantity-box">


<a
href="update_cart.php?action=decrease&id=<?php echo $row['cart_id']; ?>"
class="btn btn-outline-secondary"
>

−

</a>


<span
class="border rounded px-3 py-2"
>

<?php

echo $quantity;

?>

</span>


<a
href="update_cart.php?action=increase&id=<?php echo $row['cart_id']; ?>"
class="btn btn-outline-secondary"
>

+

</a>


</div>


</div>


<!-- ITEM TOTAL -->

<div class="col-md-2">


<strong>

Total

</strong>


<div class="price">

₹<?php

echo number_format(
    $itemTotal,
    2
);

?>

</div>


</div>


<!-- REMOVE -->

<div class="col-md-2 text-end">


<a
href="remove_from_cart.php?id=<?php echo $row['cart_id']; ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Remove this item from cart?');"
>

<i class="bi bi-trash"></i>

Remove

</a>


</div>


</div>


</div>

</div>


<?php

}

?>


</div>


<!-- ==========================================
     ORDER SUMMARY
========================================== -->

<div class="col-lg-4">


<div class="total-box">


<h4 class="mb-4">

Order Summary

</h4>


<div
class="d-flex justify-content-between mb-2"
>

<span>

Subtotal

</span>


<strong>

₹<?php

echo number_format(
    $grandTotal,
    2
);

?>

</strong>


</div>


<hr>


<div
class="d-flex justify-content-between mb-4"
>

<strong>

Grand Total

</strong>


<strong
class="text-success"
style="font-size:24px;"
>

₹<?php

echo number_format(
    $grandTotal,
    2
);

?>

</strong>


</div>


<a
href="checkout.php"
class="btn btn-success btn-lg w-100"
>

<i class="bi bi-credit-card"></i>

Proceed to Checkout

</a>


<a
href="sweets.php"
class="btn btn-outline-dark w-100 mt-2"
>

Continue Shopping

</a>


</div>


</div>


</div>


<?php

}

?>


</div>


<script
src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>


</body>

</html>