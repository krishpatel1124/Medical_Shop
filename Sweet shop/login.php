<?php

session_start();

include("config/db.php");

$error = "";

if (isset($_POST['login'])) {

    $email = mysqli_real_escape_string(
        $conn,
        trim($_POST['email'])
    );

    $password = trim($_POST['password']);

    if ($email == "" || $password == "") {

        $error = "Please enter email and password.";

    } else {

        /*
        ==================================================
        1. CHECK ADMIN
        ==================================================
        */

        $adminQuery = mysqli_query($conn, "
            SELECT *
            FROM admin
            WHERE email='$email'
            LIMIT 1
        ");

        if ($adminQuery && mysqli_num_rows($adminQuery) > 0) {

            $admin = mysqli_fetch_assoc($adminQuery);

            if ($password == $admin['password']) {

                $_SESSION['admin_id'] = $admin['admin_id'];

                if (isset($admin['admin_name'])) {
                    $_SESSION['admin_name'] = $admin['admin_name'];
                } else {
                    $_SESSION['admin_name'] = $email;
                }

                header("Location: admin/dashboard.php");
                exit();

            } else {

                $error = "Invalid email or password.";
            }

        } else {

            /*
            ==================================================
            2. CHECK SUPPLIER
            ==================================================
            */

            $supplierQuery = mysqli_query($conn, "
                SELECT *
                FROM supplier_detail
                WHERE email='$email'
                LIMIT 1
            ");

            if ($supplierQuery && mysqli_num_rows($supplierQuery) > 0) {

                $supplier = mysqli_fetch_assoc($supplierQuery);

                if ($password == $supplier['password']) {

                    $_SESSION['supplier_id'] =
                        $supplier['supplier_id'];

                    $_SESSION['supplier_name'] =
                        $supplier['supplier_name'];

                    $_SESSION['supplier_email'] =
                        $supplier['email'];

                    header("Location: supplier/dashboard.php");
                    exit();

                } else {

                    $error = "Invalid email or password.";
                }

            } else {

                /*
                ==================================================
                3. CHECK CUSTOMER
                ==================================================
                */

                $customerQuery = mysqli_query($conn, "
                    SELECT *
                    FROM customer_detail
                    WHERE email='$email'
                    LIMIT 1
                ");

                if ($customerQuery &&
                    mysqli_num_rows($customerQuery) > 0) {

                    $customer =
                        mysqli_fetch_assoc($customerQuery);

                    if ($password == $customer['password']) {

                        $_SESSION['customer_id'] =
                            $customer['customer_id'];

                        $_SESSION['customer_name'] =
                            $customer['name'];

                        $_SESSION['customer_email'] =
                            $customer['email'];

                        header("Location: index.php");
                        exit();

                    } else {

                        $error =
                            "Invalid email or password.";
                    }

                } else {

                    $error =
                        "Invalid email or password.";
                }
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1">

<title>Login - Sweet Shop</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
rel="stylesheet">

<style>

body {

    margin: 0;

    min-height: 100vh;

    display: flex;

    align-items: center;

    justify-content: center;

    background: linear-gradient(
        135deg,
        #fff5f5,
        #ffe0e0
    );

}

.login-card {

    width: 400px;

    max-width: 95%;

    background: white;

    border-radius: 15px;

    padding: 35px;

    box-shadow:
        0 10px 30px
        rgba(0,0,0,0.15);

}

.logo {

    text-align: center;

    font-size: 55px;

}

.title {

    text-align: center;

    color: #8B0000;

    font-weight: bold;

}

.subtitle {

    text-align: center;

    color: #777;

    margin-bottom: 25px;

}

.form-control {

    height: 48px;

}

.login-btn {

    width: 100%;

    height: 48px;

    background: #8B0000;

    border: none;

    color: white;

    font-weight: bold;

}

.login-btn:hover {

    background: #a30000;

    color: white;

}

</style>

</head>

<body>

<div class="login-card">

    <div class="logo">
        🍬
    </div>

    <h2 class="title">
        Sweet Shop
    </h2>

    <p class="subtitle">
        Login to continue
    </p>

    <?php if ($error != "") { ?>

        <div class="alert alert-danger">

            <?php echo htmlspecialchars($error); ?>

        </div>

    <?php } ?>

    <form method="POST">

        <div class="mb-3">

            <label class="form-label">
                Email
            </label>

            <div class="input-group">

                <span class="input-group-text">
                    <i class="bi bi-envelope"></i>
                </span>

                <input
                    type="email"
                    name="email"
                    class="form-control"
                    placeholder="Enter your email"
                    required>

            </div>

        </div>

        <div class="mb-3">

            <label class="form-label">
                Password
            </label>

            <div class="input-group">

                <span class="input-group-text">
                    <i class="bi bi-lock"></i>
                </span>

                <input
                    type="password"
                    name="password"
                    class="form-control"
                    placeholder="Enter your password"
                    required>

            </div>

        </div>

        <button
            type="submit"
            name="login"
            class="btn login-btn">

            <i class="bi bi-box-arrow-in-right"></i>

            Login

        </button>

    </form>

    <div class="text-center mt-4">

        <small>
            Don't have an account?
        </small>

        <br>

        <a href="register.php">
            Create Customer Account
        </a>

    </div>

</div>

</body>

</html>