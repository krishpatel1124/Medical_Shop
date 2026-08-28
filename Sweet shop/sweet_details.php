<?php
include("config/db.php");


if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid sweet ID.");
}

$sweet_id = intval($_GET['id']);

$query = "SELECT * FROM sweets WHERE sweet_id = $sweet_id";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    die("Sweet not found.");
}

$sweet = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?php echo htmlspecialchars($sweet['sweet_name']); ?> - Sweet Shop
    </title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>

        body {
            background: #f8f9fa;
        }

        .details-card {
            max-width: 1000px;
            margin: 50px auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            overflow: hidden;
        }

        .sweet-image {
            width: 100%;
            height: 450px;
            object-fit: cover;
        }

        .sweet-info {
            padding: 35px;
        }

        .sweet-name {
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .description {
            color: #666;
            font-size: 18px;
            margin-bottom: 25px;
        }

        .price {
            font-size: 30px;
            font-weight: bold;
            color: #198754;
        }

        .weight {
            display: inline-block;
            background: #ffc107;
            color: #000;
            padding: 6px 15px;
            border-radius: 8px;
            font-weight: bold;
            margin: 15px 0;
        }

        .available {
            color: #198754;
            font-weight: bold;
        }

        .not-available {
            color: #dc3545;
            font-weight: bold;
        }

    </style>

</head>

<body>

<div class="container">

    <div class="details-card">

        <div class="row g-0">

            <!-- IMAGE -->

            <div class="col-md-6">

                <?php if (!empty($sweet['image'])) { ?>

                    <img
                        src="uploads/sweets/<?php echo htmlspecialchars($sweet['image']); ?>"
                        class="sweet-image"
                        alt="<?php echo htmlspecialchars($sweet['sweet_name']); ?>"
                    >

                <?php } else { ?>

                    <img
                        src="uploads/sweets/kaju_katli.jpg"
                        class="sweet-image"
                        alt="No Image"
                    >

                <?php } ?>

            </div>


            <!-- DETAILS -->

            <div class="col-md-6">

                <div class="sweet-info">

                    <h1 class="sweet-name">

                        <?php
                        echo htmlspecialchars($sweet['sweet_name']);
                        ?>

                    </h1>


                    <p class="description">

                        <?php
                        echo htmlspecialchars($sweet['description']);
                        ?>

                    </p>


                    <div class="price">

                        ₹<?php
                        echo number_format($sweet['price'], 2);
                        ?>

                    </div>


                    <div>

                        <span class="weight">

                            <?php
                            echo htmlspecialchars($sweet['weight']);
                            ?>

                        </span>

                    </div>


                    <!-- AVAILABILITY -->
                    
                    <?php


                    if (
                        strtolower($sweet['is_available']) == 'yes' ||
                        $sweet['is_available'] == '1'
                    ) {

                    ?>

                        <p class="available">
                            ✓ Available
                        </p>

                    <?php

                    } else {

                    ?>

                        <p class="not-available">
                            ✕ Currently Unavailable
                        </p>

                    <?php

                    }

                    ?>


                    <hr>


                    <!-- BACK BUTTON -->

                    <a
                        href="sweets.php"
                        class="btn btn-secondary"
                    >
                        ← Back to Sweets
                    </a>


                    <!-- ADD TO CART -->

                    <?php

                    if (
                        strtolower($sweet['is_available']) == 'yes' ||
                        $sweet['is_available'] == '1'
                    ) {

                    ?>

                  <a href="add_to_cart.php?id=<?php echo $sweet['sweet_id']; ?>"
                   class="btn btn-success">
                    🛒 Add to Cart
</a>

                    <?php } ?>


                </div>

            </div>

        </div>

    </div>

</div>

</body>

</html>