<?php
session_start();
include("config/db.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Online Sweet Shopping & Storage System</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f8f9fa;
        }

        /* Navbar */

        .navbar {
            background: #0e120f;
        }

        .navbar-logo img {
            height: 40px;
            width: 40px;
            display: block;
        }

        .navbar-brand {
            color: white;
            font-size: 28px;
            font-weight: bold;
        }

        .navbar-brand:hover {
            color: #FFD700;
        }

        .nav-link {
            color: white !important;
            font-weight: 500;
        }

        .nav-link:hover {
            color: #FFD700 !important;
        }

        /* Carousel */

        .carousel-item img {
            height: 550px;
            object-fit: cover;
        }

        .carousel-caption {
            background: rgba(0, 0, 0, .55);
            padding: 20px;
            border-radius: 10px;
        }

        .carousel-caption h2 {
            font-size: 42px;
            font-weight: bold;
        }

        .carousel-caption p {
            font-size: 18px;
        }

        /* Welcome */

        .welcome {
            padding: 60px 20px;
            text-align: center;
        }

        .welcome h2 {
            color: #8B0000;
            font-weight: bold;
        }

        .welcome p {
            font-size: 18px;
            color: #555;
        }
    </style>

</head>

<body>

    <!-- Navbar -->

    <nav class="navbar navbar-expand-lg">

        <div class="container">

            <a class="navbar-brand" href="index.php">
                 Krishna Sweets
            </a>

            <button class="navbar-toggler bg-light"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#menu">

                <span class="navbar-toggler-icon"></span>

            </button>

            <div class="collapse navbar-collapse" id="menu">

                <ul class="navbar-nav ms-auto">

                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="sweets.php">Sweets</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="cart.php">Cart</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="about.php">About</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact</a>
                    </li>

                    <?php

                    if (isset($_SESSION['user_id'])) {

                    ?>

                        <li class="nav-item">
                            <a class="nav-link" href="profile.php">

                                Welcome,
                                <?php echo $_SESSION['user_name']; ?>

                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link text-warning" href="logout.php">
                                Logout
                            </a>
                        </li>

                    <?php

                    } else {

                    ?>

                        <li class="nav-item">
                            <a class="nav-link" href="login.php">
                                Log-out
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="register.php">
                                Register
                            </a>
                        </li>

                    <?php

                    }

                    ?>

                </ul>

            </div>

        </div>

    </nav>

    <!-- Slider -->

    <div id="slider" class="carousel slide" data-bs-ride="carousel">

        <div class="carousel-inner">

            <div class="carousel-item active">

                <img src="assets/images/banner1.jpg"
                    class="d-block w-100">

                <div class="carousel-caption">

                    <h2>Welcome to Sweet Shop</h2>

                    <p>Fresh, Delicious and Hygienic Indian Sweets</p>

                    <a href="sweets.php"
                        class="btn btn-warning btn-lg">

                        Shop Now

                    </a>

                </div>

            </div>

            <div class="carousel-item">

                <img src="assets/images/banner2.jpg"
                    class="d-block w-100">

                <div class="carousel-caption">

                    <h2>Festival Special Sweets</h2>


                    <a href="sweets.php"
                        class="btn btn-success btn-lg">

                        Explore

                    </a>

                </div>

            </div>

            <div class="carousel-item">

                <img src="assets/images/banner3.jpg"
                    class="d-block w-100">

                <div class="carousel-caption">

                    <h2>Premium Quality Sweets</h2>

                    <p>Made with Pure Ghee & Fresh Ingredients</p>

                    <a href="sweets.php"
                        class="btn btn-danger btn-lg">

                        Order Now

                    </a>

                </div>

            </div>

        </div>

        <button class="carousel-control-prev"
            type="button"
            data-bs-target="#slider"
            data-bs-slide="prev">

            <span class="carousel-control-prev-icon"></span>

        </button>

        <button class="carousel-control-next"
            type="button"
            data-bs-target="#slider"
            data-bs-slide="next">

            <span class="carousel-control-next-icon"></span>

        </button>

    </div>

    <!-- Welcome Section -->

    <section class="welcome">

        <div class="container">

            <h2>Krishna Sweets</h2>

            <p>

                Order your favourite sweets online with secure payment,
                fast delivery, and quality assurance.
                Our storage management ensures every sweet is fresh and
                available for customers.

            </p>

        </div>

    </section>


<!-- Featured Sweets -->

<section class="container py-5">

    <div class="text-center mb-5">
        <h2 class="fw-bold text-danger">Featured Sweets</h2>
        <p class="text-muted">Fresh & Delicious Indian Sweets</p>
    </div>

    <div class="row">

        <?php

        $query = "SELECT *
                  FROM sweets
                  WHERE is_available = 'yes'
                  LIMIT 8";

        $result = mysqli_query($conn, $query);

        if (!$result) {
            die("Sweet Query Error: " . mysqli_error($conn));
        }

        if (mysqli_num_rows($result) > 0) {

            while ($row = mysqli_fetch_assoc($result)) {

        ?>

                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">

                    <div class="card h-100 shadow">

                        <img
                            src="uploads/sweets/<?php echo htmlspecialchars($row['image']); ?>"
                            class="card-img-top"
                            style="height:220px;object-fit:cover;"
                            alt="<?php echo htmlspecialchars($row['sweet_name']); ?>">

                        <div class="card-body text-center">

                            <h5 class="card-title">
                                <?php echo htmlspecialchars($row['sweet_name']); ?>
                            </h5>

                            <p class="text-muted">
                                <?php echo htmlspecialchars($row['description']); ?>
                            </p>

                            <h4 class="text-success">
                                ₹<?php echo number_format($row['price'], 2); ?>
                            </h4>

                            <p>
                                <span class="badge bg-warning text-dark">
                                    <?php echo htmlspecialchars($row['weight']); ?>
                                </span>
                            </p>

                            <a
                                href="sweet_details.php?id=<?php echo $row['sweet_id']; ?>"
                                class="btn btn-outline-primary">
                                View
                            </a>

                            <a
                                href="add_to_cart.php?id=<?php echo $row['sweet_id']; ?>"
                                class="btn btn-success">
                                Add to Cart
                            </a>

                        </div>

                    </div>

                </div>

        <?php

            }

        } else {

        ?>

            <div class="col-12">
                <div class="alert alert-danger text-center">
                    No sweets available.
                </div>
            </div>

        <?php

        }

        ?>

    </div>

</section>
    <!-- Why Choose Us -->

    <section class="bg-light py-5">

        <div class="container">

            <div class="row text-center">

                <div class="col-md-3">

                    <h1>🍬</h1>

                    <h5>Fresh Sweets</h5>

                    <p>Prepared daily using fresh ingredients.</p>

                </div>

                <div class="col-md-3">

                    <h1>🚚</h1>

                    <h5>Fast Delivery</h5>

                    <p>Quick and safe doorstep delivery.</p>

                </div>

                <div class="col-md-3">

                    <h1>💳</h1>

                    <h5>Secure Payment</h5>

                    <p>Multiple secure payment options.</p>

                </div>

                <div class="col-md-3">

                    <h1>⭐</h1>

                    <h5>Best Quality</h5>

                    <p>Premium quality with hygiene assurance.</p>

                </div>

            </div>

        </div>

    </section>


    

    <!-- Shop By Categories -->

    <section class="container py-5">

        <div class="text-center mb-5">

            <h2 class="text-danger fw-bold">
                Shop By Categories
            </h2>

            <p>Select your favourite sweet category</p>

        </div>

        <div class="row">

            <?php

            $category = mysqli_query($conn, "SELECT * FROM category LIMIT 6");

            while ($cat = mysqli_fetch_assoc($category)) {

            ?>

                <div class="col-lg-4 col-md-6 mb-4">

                    <div class="card shadow h-100">

                        <div class="card-body text-center">

                            <h4>
                                <?php echo $cat['category_name']; ?>
                            </h4>

                            <p>

                                Explore delicious
                                <?php echo $cat['category_name']; ?>
                                collection.

                            </p>

                            <a href="sweets.php?category=<?php echo $cat['category_id']; ?>"
                                class="btn btn-outline-danger">

                                View Products

                            </a>

                        </div>

                    </div>

                </div>

            <?php
            }
            ?>

        </div>

    </section>

    <!-- Why Customers Love Us -->

    <section class="container py-5">

        <div class="row text-center">

            <div class="col-md-3">

                <h1>🍬</h1>

                <h4>Fresh Everyday</h4>

                <p>Prepared using premium ingredients.</p>

            </div>

            <div class="col-md-3">

                <h1>🚚</h1>

                <h4>Fast Delivery</h4>

                <p>Quick delivery across your city.</p>

            </div>

            <div class="col-md-3">

                <h1>💳</h1>

                <h4>Secure Payment</h4>

                <p>100% safe online payment.</p>

            </div>

            <div class="col-md-3">

                <h1>⭐</h1>

                <h4>Best Quality</h4>

                <p>Trusted by hundreds of customers.</p>

            </div>

        </div>

    </section>


    <!-- Call To Action -->

    <section class="container-fluid bg-dark text-white py-5">

        <div class="container text-center">

            <h2>

                Order Your Favourite Sweets Today

            </h2>

            <p>

                Fresh • Hygienic • Delicious

            </p>

            <a href="sweets.php"
                class="btn btn-success btn-lg">

                Start Shopping

            </a>

        </div>

    </section>
    <!-- ===================== Testimonials ===================== -->

    <section class="container py-5">

        <div class="text-center mb-5">
            <h2 class="text-danger fw-bold">What Our Customers Say</h2>
            <p>Trusted by hundreds of happy customers.</p>
        </div>

        <div class="row">

            <div class="col-md-4 mb-4">
                <div class="card shadow h-100">
                    <div class="card-body text-center">

                        <img src="assets/images/user1.png"
                            width="90"
                            height="90"
                            class="rounded-circle mb-3">

                        <h5>Rahul Patel</h5>

                        <p class="text-warning">
                            ★★★★★
                        </p>

                        <p>
                            "The sweets were fresh and delicious.
                            Delivery was quick and the packaging was excellent."
                        </p>

                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card shadow h-100">
                    <div class="card-body text-center">

                        <img src="assets/images/user2.png"
                            width="90"
                            height="90"
                            class="rounded-circle mb-3">

                        <h5>Priya Shah</h5>

                        <p class="text-warning">
                            ★★★★★
                        </p>

                        <p>
                            "I ordered Kaju Katli for Diwali.
                            The quality was amazing. Highly recommended!"
                        </p>

                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card shadow h-100">
                    <div class="card-body text-center">

                        <img src="assets/images/user3.png"
                            width="90"
                            height="90"
                            class="rounded-circle mb-3">

                        <h5>Amit Kumar</h5>

                        <p class="text-warning">
                            ★★★★★
                        </p>

                        <p>
                            "Very easy ordering process and secure payment.
                            Will definitely order again."
                        </p>

                    </div>
                </div>
            </div>

        </div>

    </section>

    <!-- ===================== Footer ===================== -->

    <footer class="bg-dark text-white pt-5 pb-3">

        <div class="container">

            <div class="row">

                <div class="col-md-4">

                    <h3 class="text-warning">
                        🍬 krishna sweets 
                    </h3>

                    <p>

                        Fresh sweets made with premium ingredients,
                        delivered with love and care.

                    </p>

                </div>

                <div class="col-md-2">

                    <h5>Quick Links</h5>

                    <ul class="list-unstyled">

                        <li><a href="index.php" class="text-white text-decoration-none">Home</a></li>

                        <li><a href="sweets.php" class="text-white text-decoration-none">Sweets</a></li>

                        <li><a href="cart.php" class="text-white text-decoration-none">Cart</a></li>

                        <li><a href="contact.php" class="text-white text-decoration-none">Contact</a></li>

                    </ul>

                </div>

                <div class="col-md-3">

                    <h5>Contact Us</h5>

                    <p>
                        📍 valsad, Gujarat
                    </p>

                    <p>
                        📞 +91 7383757333
                    </p>

                    <p>
                        ✉️ krishnasweets13@gmail.com
                    </p>

                </div>

                <div class="col-md-3">

                    <h5>Follow Us</h5>

                    <a href="#" class="btn btn-outline-light btn-sm m-1">
                        Facebook
                    </a>

                    <a href="#" class="btn btn-outline-light btn-sm m-1">
                        Instagram
                    </a>

                    <a href="#" class="btn btn-outline-light btn-sm m-1">
                        WhatsApp
                    </a>

                    <a href="#" class="btn btn-outline-light btn-sm m-1">
                        YouTube
                    </a>

                </div>

            </div>

            <hr>

            <div class="text-center">

                <p class="mb-0">

                    © 2026 Online Sweet Shopping & Storage System.
                    All Rights Reserved.

                </p>

            </div>

        </div>

    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>