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
// CHECK SWEET ID
// ==========================================

if (!isset($_GET['id'])) {

    header("Location: sweets.php");
    exit();

}

$sweet_id = (int)$_GET['id'];


// ==========================================
// GET SELECTED WEIGHT
// ==========================================

if (isset($_GET['weight'])) {

    $weight = (int)$_GET['weight'];

} else {

    $weight = 1000;

}


// ==========================================
// VALID WEIGHTS
// ==========================================

$allowed_weights = array(

    100,
    150,
    200,
    250,
    300,
    350,
    400,
    450,
    500,
    550,
    600,
    650,
    700,
    750,
    800,
    850,
    900,
    950,
    1000,
    2000,
    3000,
    4000,
    5000

);


if (!in_array($weight, $allowed_weights)) {

    $weight = 1000;

}


// ==========================================
// GET SWEET FROM DATABASE
// ==========================================

$sweetQuery = mysqli_query(
    $conn,
    "SELECT *
     FROM sweets
     WHERE sweet_id='$sweet_id'
     LIMIT 1"
);


if (!$sweetQuery || mysqli_num_rows($sweetQuery) == 0) {

    header("Location: sweets.php");
    exit();

}


$sweet = mysqli_fetch_assoc($sweetQuery);


// ==========================================
// CHECK AVAILABILITY
// ==========================================

$is_available = strtolower(
    trim($sweet['is_available'])
);


if (
    $is_available != 'yes' &&
    $is_available != '1' &&
    $is_available != 'available'
) {

    header("Location: sweets.php?error=unavailable");
    exit();

}


// ==========================================
// GET BASE WEIGHT
// ==========================================

$baseWeightText = trim($sweet['weight']);

$baseWeightLower = strtolower($baseWeightText);

$baseWeight = 1000;


// If weight contains KG

if (strpos($baseWeightLower, 'kg') !== false) {

    $number = (float) filter_var(
        $baseWeightLower,
        FILTER_SANITIZE_NUMBER_FLOAT,
        FILTER_FLAG_ALLOW_FRACTION
    );

    $baseWeight = $number * 1000;

}


// If weight contains G

else if (strpos($baseWeightLower, 'g') !== false) {

    $number = (float) filter_var(
        $baseWeightLower,
        FILTER_SANITIZE_NUMBER_FLOAT,
        FILTER_FLAG_ALLOW_FRACTION
    );

    $baseWeight = $number;

}


if ($baseWeight <= 0) {

    $baseWeight = 1000;

}


// ==========================================
// CALCULATE PRICE
// ==========================================

$basePrice = (float)$sweet['price'];

$selectedPrice =
    ($weight / $baseWeight) * $basePrice;


// Round to 2 decimals

$selectedPrice = round(
    $selectedPrice,
    2
);


// ==========================================
// CHECK WHETHER SAME SWEET + SAME WEIGHT
// ALREADY EXISTS IN CART
// ==========================================

$checkQuery = mysqli_query(
    $conn,
    "SELECT *
     FROM cart
     WHERE customer_id='$customer_id'
     AND sweet_id='$sweet_id'
     AND weight='$weight'
     LIMIT 1"
);


if (!$checkQuery) {

    die(
        "Cart check error: " .
        mysqli_error($conn)
    );

}


// ==========================================
// IF EXISTS → INCREASE QUANTITY
// ==========================================

if (mysqli_num_rows($checkQuery) > 0) {

    $cart = mysqli_fetch_assoc($checkQuery);

    $newQuantity =
        (int)$cart['quantity'] + 1;


    $updateQuery = mysqli_query(
        $conn,
        "UPDATE cart
         SET
            quantity='$newQuantity',
            price='$selectedPrice'
         WHERE cart_id='{$cart['cart_id']}'"
    );


    if (!$updateQuery) {

        die(
            "Cart update error: " .
            mysqli_error($conn)
        );

    }

}


// ==========================================
// OTHERWISE INSERT NEW CART ITEM
// ==========================================

else {

    $insertQuery = mysqli_query(
        $conn,
        "INSERT INTO cart
        (
            customer_id,
            sweet_id,
            weight,
            price,
            quantity
        )
        VALUES
        (
            '$customer_id',
            '$sweet_id',
            '$weight',
            '$selectedPrice',
            '1'
        )"
    );


    if (!$insertQuery) {

        die(
            "Cart insert error: " .
            mysqli_error($conn)
        );

    }

}


// ==========================================
// GO TO CART
// ==========================================

header("Location: cart.php");
exit();

?>