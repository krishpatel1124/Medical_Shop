<?php

// Start session only if it is not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/*
==================================================
CUSTOMER SESSION
==================================================
*/

function isCustomerLoggedIn()
{
    return isset($_SESSION['customer_id']);
}

function requireCustomer()
{
    if (!isset($_SESSION['customer_id'])) {
        header("Location: /Sweet%20shop/login.php");
        exit();
    }
}


/*
==================================================
ADMIN SESSION
==================================================
*/

function isAdminLoggedIn()
{
    return isset($_SESSION['admin_id']);
}

function requireAdmin()
{
    if (!isset($_SESSION['admin_id'])) {
        header("Location: /Sweet%20shop/login.php");
        exit();
    }
}


/*
==================================================
SUPPLIER SESSION
==================================================
*/

function isSupplierLoggedIn()
{
    return isset($_SESSION['supplier_id']);
}

function requireSupplier()
{
    if (!isset($_SESSION['supplier_id'])) {
        header("Location: /Sweet%20shop/login.php");
        exit();
    }
}


/*
==================================================
LOGOUT
==================================================
*/

function logoutUser()
{
    $_SESSION = array();

    if (ini_get("session.use_cookies")) {

        $params = session_get_cookie_params();

        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    session_destroy();
}

?>