<?php

session_start();

include("config/db.php");

if (!isset($_SESSION['customer_id'])) {

    header("Location: login.php");
    exit();

}

$customer_id = (int)$_SESSION['customer_id'];


if (!isset($_GET['id'])) {

    header("Location: cart.php");
    exit();

}


$cart_id = (int)$_GET['id'];


mysqli_query(
    $conn,
    "DELETE FROM cart
     WHERE cart_id='$cart_id'
     AND customer_id='$customer_id'"
);


header("Location: cart.php");

exit();

?>