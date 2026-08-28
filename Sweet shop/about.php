<?php
session_start();
include("config/db.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>About Us - Krishna Sweets</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body {
    background-color: #f8f9fa;
    font-family: Arial, sans-serif;
}

/* Navbar */
 .nav-link:hover {
            color: #FFD700 !important;
        }

.navbar-brand {
    font-weight: bold;
}

/* Main About Section */

.about-section {
    background: white;
    padding: 50px;
    margin-top: 50px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}

/* Heading */

.about-title {
    color: #dc3545;
    font-weight: bold;
}

/* Text */

.about-text {
    color: #555;
    line-height: 1.8;
    font-size: 16px;
}

/* Small Boxes */

.info-box {
    background: #fff;
    padding: 25px;
    border-radius: 8px;
    border: 1px solid #ddd;
    height: 100%;
}

.info-box h4 {
    color: #dc3545;
    margin-bottom: 15px;
}

/* Footer */

footer {
    margin-top: 60px;
    background: #212529;
    color: white;
    padding: 25px;
    text-align: center;
}

</style>

</head>

<body>


<!-- ================= NAVBAR ================= -->

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">

    <div class="container">
      <a class="navbar-brand" href="index.php">
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
                        class="nav-link"
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
                        class="nav-link active"
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


<!-- ================= ABOUT ================= -->

<div class="container">

    <div class="about-section">
     <h1 class="text-center about-title mb-4">
        About Krishna Sweets
     </h1>

        <p class="text-center about-text">

            Welcome to <strong>krishna sweets</strong>, your trusted place
            for delicious and traditional Indian sweets.

        </p>

        <p class="about-text">

            We provide a variety of fresh and tasty sweets prepared
            with quality ingredients. Our goal is to provide our
            customers with delicious sweets for every occasion.

        </p>

        <p class="about-text">

            From traditional Mithai to delicious snacks and special
            gift boxes, we have something for everyone. You can easily
            browse our sweets, select your required quantity and place
            your order online.

        </p>

        <p class="about-text">

            We believe that sweets are an important part of happiness
            and celebration. Whether it is a festival, birthday,
            wedding or a simple family gathering, Sweet Shop is here
            to make your occasion sweeter.

        </p>

    </div>


    <!-- ================= OUR SERVICES ================= -->

    <div class="row mt-4">

        <div class="col-md-4 mb-4">

            <div class="info-box">

                <h4>
                    Fresh Sweets
                </h4>

                <p class="text-muted">

                    We provide fresh and delicious sweets made with
                    quality ingredients.

                </p>

            </div>

        </div>


        <div class="col-md-4 mb-4">

            <div class="info-box">

                <h4>
                    Wide Variety
                </h4>

                <p class="text-muted">

                    Choose from traditional Mithai, cakes, snacks
                    and gift boxes.

                </p>

            </div>

        </div>


        <div class="col-md-4 mb-4">

            <div class="info-box">

                <h4>
                    Easy Ordering
                </h4>

                <p class="text-muted">

                    Browse our sweets, select quantity and easily
                    add your favourite sweets to the cart.

                </p>

            </div>

        </div>

    </div>


    <!-- ================= WHY CHOOSE US ================= -->

    <div class="about-section mt-4">

        <h2 class="text-center about-title mb-4">

            Why Choose Us?

        </h2>

        <div class="row">

            <div class="col-md-6">

                <p class="about-text">
                    ✓ Quality ingredients
                </p>

                <p class="about-text">
                    ✓ Fresh and tasty sweets
                </p>

                <p class="about-text">
                    ✓ Affordable prices
                </p>

            </div>

            <div class="col-md-6">

                <p class="about-text">
                    ✓ Easy online ordering
                </p>

                <p class="about-text">
                    ✓ Variety of sweets
                </p>

                <p class="about-text">
                    ✓ Customer satisfaction
                </p>

            </div>

        </div>

    </div>

</div>


<footer>

    <p class="mb-1">
        <strong>Krishna Sweets</strong>
    </p>

    <p class="mb-0">
        Delicious sweets for every occasion.
    </p>

    <p class="mt-2 mb-0">
        © <?php echo date("Y"); ?> Krishna Sweets. All Rights Reserved.
    </p>

</footer>   
<script
src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

</body>

</html>