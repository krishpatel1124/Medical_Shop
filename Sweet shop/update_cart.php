<?php

session_start();

include("config/db.php");

if (!isset($_SESSION['customer_id'])) {

    header("Location: login.php");
    exit();

}

$customer_id = (int)$_SESSION['customer_id'];

if (!isset($_GET['id']) || !isset($_GET['action'])) {

    header("Location: cart.php");
    exit();

}

$cart_id = (int)$_GET['id'];

$action = $_GET['action'];


// Get current cart item

$query = mysqli_query(
    $conn,
    "SELECT quantity
     FROM cart
     WHERE cart_id='$cart_id'
     AND customer_id='$customer_id'"
);


if (!$query || mysqli_num_rows($query) == 0) {

    header("Location: cart.php");
    exit();

}


$row = mysqli_fetch_assoc($query);

$quantity = (int)$row['quantity'];


// Increase

if ($action == "increase") {

    $quantity++;

}


// Decrease

elseif ($action == "decrease") {

    $quantity--;

}


// Never allow quantity below 1

if ($quantity < 1) {

    $quantity = 1;

}


mysqli_query(
    $conn,
    "UPDATE cart
     SET quantity='$quantity'
     WHERE cart_id='$cart_id'
     AND customer_id='$customer_id'"
);


header("Location: cart.php");

exit();

?>