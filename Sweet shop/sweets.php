<?php

session_start();
include("config/db.php");


// ==========================================
// SEARCH
// ==========================================




// ==========================================
// GET ALL SWEETS
// ==========================================



?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>Our Sweets - Krishna Sweets</title>


<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">


<style>

/* ================= BODY ================= */

body {

    background: #f8f9fa;

}


/* ================= NAVBAR ================= */

.nav-link:hover {

    color: #FFD700 !important;

}

.navbar-brand {

    font-weight: bold;

}


/* ================= SWEET CARD ================= */

.card {

    transition: 0.3s;

    border: none;

    border-radius: 12px;

    overflow: hidden;

}


.card:hover {

    transform: translateY(-5px);

    box-shadow:
        0 8px 20px
        rgba(0,0,0,0.15);

}


.card img {

    height: 220px;

    width: 100%;

    object-fit: cover;

}


/* ================= PRICE ================= */

.price {

    font-size: 22px;

    font-weight: bold;

    color: #198754;

}


/* ================= WEIGHT ================= */

.weight-label {

    font-weight: 600;

}


/* ================= BUTTON ================= */

.add-btn {

    width: 100%;

}


/* ================= AVAILABILITY ================= */

.available {

    color: #198754;

    font-weight: bold;

}


.unavailable {

    color: #dc3545;

    font-weight: bold;

}

</style>

</head>


<body>


<!-- ================================================== -->
<!-- NAVBAR -->
<!-- ================================================== -->

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">


<div class="container">


<a
class="navbar-brand"
href="index.php">

Krishna Sweets

</a>


<button
class="navbar-toggler"
type="button"
data-bs-toggle="collapse"
data-bs-target="#menu">

<span class="navbar-toggler-icon"></span>

</button>


<div
class="collapse navbar-collapse"
id="menu">


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
class="nav-link active"
href="sweets.php">

Sweets

</a>

</li>


<li class="nav-item">

<a
class="nav-link"
href="cart.php">

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

<!-- ================================================== -->
<!-- SEARCH -->
<!-- ================================================== -->

<section class="container py-5">
<?php
$search = $_GET['search'] ?? '';

$search = mysqli_real_escape_string($conn, $search);

$query = mysqli_query($conn, "
    SELECT *
    FROM sweets
    WHERE sweet_name LIKE '%$search%'
    ORDER BY sweet_name ASC
");
?>
<div class="row justify-content-center">


<div class="col-md-8">


<form
action="sweets.php"
method="GET">


<div class="input-group">


<input
type="text"
name="search"
class="form-control form-control-lg"
placeholder="Search your favourite sweets..."
value="<?php echo ($search); ?>">


<button
type="submit"
class="btn btn-danger btn-lg">

Search

</button>


</div>


</form>


</div>


</div>


</section>



<!-- ================================================== -->
<!-- SWEETS -->
<!-- ================================================== -->

<div class="container py-5">


<h2 class="text-center mb-5">

Our Delicious Sweets

</h2>


<div class="row">


<?php


if (mysqli_num_rows($query) > 0) {


    while ($row = mysqli_fetch_assoc($query)) {


        // ==========================================
        // SWEET ID
        // ==========================================

        $sweetId = (int)$row['sweet_id'];


        // ==========================================
        // SWEET NAME
        // ==========================================

        $sweetName = $row['sweet_name'];


        // ==========================================
        // DESCRIPTION
        // ==========================================

        $description = $row['description'];


        // ==========================================
        // IMAGE
        // ==========================================

        $image = $row['image'];


        // ==========================================
        // BASE PRICE
        // ==========================================

        $basePrice = (float)$row['price'];


        // ==========================================
        // DATABASE WEIGHT
        // ==========================================

        $baseWeightText = trim(
            $row['weight']
        );


        $baseWeightLower = strtolower(
            $baseWeightText
        );


        // Extract number

        $baseWeight = (float)filter_var(

            $baseWeightLower,

            FILTER_SANITIZE_NUMBER_FLOAT,

            FILTER_FLAG_ALLOW_FRACTION

        );


        // ==========================================
        // CONVERT KG TO GRAMS
        // ==========================================

        if (
            strpos(
                $baseWeightLower,
                'kg'
            ) !== false
        ) {

            $baseWeight =
                $baseWeight * 1000;

        }


        // ==========================================
        // DEFAULT WEIGHT
        // ==========================================

        if ($baseWeight <= 0) {

            $baseWeight = 1000;

        }


        // ==========================================
        // AVAILABILITY
        // ==========================================

        $isAvailable = strtolower(

            trim(
                $row['is_available']
            )

        );


?>


<!-- ================================================== -->
<!-- SWEET CARD -->
<!-- ================================================== -->

<div class="col-md-6 col-lg-4 col-xl-3 mb-4">


<div class="card shadow h-100">


<!-- IMAGE -->

<img
src="uploads/sweets/<?php echo htmlspecialchars($image); ?>"
class="card-img-top"
alt="<?php echo htmlspecialchars($sweetName); ?>">


<div class="card-body d-flex flex-column">


<!-- NAME -->

<h5 class="card-title">

<?php

echo htmlspecialchars(
    $sweetName
);

?>

</h5>


<!-- DESCRIPTION -->

<p class="text-muted small">

<?php

echo htmlspecialchars(
    $description
);

?>

</p>


<!-- BASE PRICE -->

<p class="mb-1">


<strong>

Base Price:

</strong>


₹<?php

echo number_format(
    $basePrice,
    2
);

?>


/


<?php

echo htmlspecialchars(
    $baseWeightText
);

?>


</p>



<!-- ================================================== -->
<!-- WEIGHT DROPDOWN -->
<!-- ================================================== -->

<div class="mb-3">


<label
for="weight_<?php echo $sweetId; ?>"
class="form-label weight-label">

Select Quantity

</label>


<select
class="form-select weight-select"
id="weight_<?php echo $sweetId; ?>"
data-sweet-id="<?php echo $sweetId; ?>"
data-base-price="<?php echo $basePrice; ?>"
data-base-weight="<?php echo $baseWeight; ?>">


<option value="100">100 g</option>

<option value="150">150 g</option>

<option value="200">200 g</option>

<option value="250">250 g</option>

<option value="300">300 </option>

<option value="350">350 g</option>

<option value="400">400 g</option>

<option value="450">450 g</option>

<option value="500">500 g</option>

<option value="550">550 g</option>

<option value="600">600 g</option>

<option value="650">650 g</option>

<option value="700">700 g</option>

<option value="750">750 g</option>

<option value="800">800 g</option>

<option value="850">850 g</option>

<option value="900">900 g</option>

<option value="950">950 g</option>

<option value="1000">1 kg</option>

<option value="2000">2 kg</option>

<option value="3000">3 kg</option>

<option value="4000">4 kg</option>

<option value="5000">5 kg</option>

</select>

</div>



<!-- ================================================== -->
<!-- PRICE -->
<!-- ================================================== -->

<div class="mb-3">


<div>


<strong>

Price:

</strong>


<span
class="price"
id="price_<?php echo $sweetId; ?>">

₹0.00

</span>


</div>


<small class="text-muted">

Price changes according to selected quantity.

</small>


</div>

<!-- AVAILABILITY -->

<div class="mb-3">


<?php


if (

    $isAvailable == 'yes' ||

    $isAvailable == '1' ||

    $isAvailable == 'available'

) {


?>


<span class="available">

 Available

</span>


<?php


} else {


?>


<span class="unavailable">

 Currently Unavailable

</span>


<?php


}


?>


</div>



<!-- ================================================== -->
<!-- ADD TO CART -->
<!-- ================================================== -->

<?php


if (

    $isAvailable == 'yes' ||

    $isAvailable == '1' ||

    $isAvailable == 'available'

) {


?>


<a
href="#"
class="btn btn-warning add-btn mt-auto add-cart-btn"
id="cart_<?php echo $sweetId; ?>"
data-sweet-id="<?php echo $sweetId; ?>">

Add to Cart

</a>


<?php


} else {


?>


<button
class="btn btn-secondary add-btn mt-auto"
disabled>

Currently Unavailable

</button>


<?php


}


?>


</div>


</div>


</div>


<?php


    }


} else {


?>


<!-- ================================================== -->
<!-- NO SWEETS -->
<!-- ================================================== -->

<div class="col-12">


<div class="alert alert-warning text-center">

<?php

if ($search != '') {

    echo "No sweets found for: ";

    echo "<strong>";

    echo htmlspecialchars($search);

    echo "</strong>";

} else {

    echo "No sweets available.";

}

?>

</div>


</div>


<?php


}


?>


</div>


</div>



<!-- ================================================== -->
<!-- JAVASCRIPT -->
<!-- ================================================== -->

<script>

document.addEventListener(
    "DOMContentLoaded",
    function()
    {


        // ==========================================
        // FIND ALL DROPDOWNS
        // ==========================================

        const dropdowns =
            document.querySelectorAll(
                ".weight-select"
            );


        dropdowns.forEach(
            function(select)
            {


                // ==================================
                // SWEET ID
                // ==================================

                const sweetId =
                    select.dataset.sweetId;


                // ==================================
                // BASE PRICE
                // ==================================

                const basePrice =
                    parseFloat(
                        select.dataset.basePrice
                    );


                // ==================================
                // BASE WEIGHT
                // ==================================

                const baseWeight =
                    parseFloat(
                        select.dataset.baseWeight
                    );


                // ==================================
                // PRICE ELEMENT
                // ==================================

                const priceElement =
                    document.getElementById(
                        "price_" + sweetId
                    );


                // ==================================
                // CART BUTTON
                // ==================================

                const cartButton =
                    document.getElementById(
                        "cart_" + sweetId
                    );


                // ==================================
                // UPDATE PRICE
                // ==================================

                function updatePrice()
                {


                    const selectedWeight =
                        parseFloat(
                            select.value
                        );


                    // Calculate price

                    const calculatedPrice =

                        (
                            selectedWeight /
                            baseWeight
                        )
                        *
                        basePrice;


                    // Show price

                    priceElement.innerHTML =

                        "₹" +
                        calculatedPrice.toFixed(2);


                    // ==================================
                    // UPDATE CART URL
                    // ==================================

                    if (cartButton) {


                        cartButton.href =

                            "add_to_cart.php" +

                            "?id=" +
                            sweetId +

                            "&weight=" +
                            selectedWeight +

                            "&price=" +
                            calculatedPrice.toFixed(2);


                    }


                }


                // ==================================
                // INITIAL PRICE
                // ==================================

                updatePrice();


                // ==================================
                // WHEN QUANTITY CHANGES
                // ==================================

                select.addEventListener(
                    "change",
                    updatePrice
                );


            }
        );


    }
);

</script>



<!-- ================================================== -->
<!-- BOOTSTRAP JS -->
<!-- ================================================== -->

<script
src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>


</body>

</html>