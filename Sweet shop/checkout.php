<?php

session_start();
include("config/db.php");


// ==================================================
// 1. CHECK CUSTOMER LOGIN
// ==================================================

if (!isset($_SESSION['customer_id'])) {

    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];


// ==================================================
// 2. GET CUSTOMER DETAILS
// ==================================================

$customerQuery = mysqli_query(
    $conn,
    "
    SELECT *
    FROM customer_detail
    WHERE customer_id = '$customer_id'
    LIMIT 1
    "
);

if (!$customerQuery || mysqli_num_rows($customerQuery) == 0) {

    echo "Customer details not found.";
    exit();
}

$customer = mysqli_fetch_assoc($customerQuery);


// ==================================================
// 3. GET CART ITEMS
// ==================================================

$cartQuery = mysqli_query(
    $conn,
    "
    SELECT
        c.cart_id,
        c.quantity,
        s.sweet_id,
        s.sweet_name,
        s.price,
        s.image
    FROM cart c
    INNER JOIN sweets s
        ON c.sweet_id = s.sweet_id
    WHERE c.customer_id = '$customer_id'
    "
);


// Check cart
if (!$cartQuery || mysqli_num_rows($cartQuery) == 0) {

    header("Location: cart.php");
    exit();
}


// ==================================================
// 4. CALCULATE GRAND TOTAL
// ==================================================

$grandTotal = 0;

$cartItems = [];

while ($item = mysqli_fetch_assoc($cartQuery)) {

    $subtotal =
        $item['price'] * $item['quantity'];

    $grandTotal += $subtotal;

    $cartItems[] = $item;
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1">

<title>Checkout - Sweet Shop</title>


<!-- Bootstrap -->

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">


<style>

body {

    background:#fff5f5;

}


.checkout-title {

    color:#8B0000;

    font-weight:bold;

}


.card {

    border:none;

    border-radius:15px;

}


.card-header {

    font-weight:bold;

}


.customer-card {

    border-left:5px solid #8B0000;

}


.order-card {

    border-left:5px solid #198754;

}


.total-row {

    font-size:20px;

    font-weight:bold;

}


.payment-box {

    background:#f8f9fa;

    border-radius:10px;

    padding:20px;

}


.place-order-btn {

    background:#198754;

    border:none;

    padding:12px;

    font-size:18px;

    font-weight:bold;

}


.place-order-btn:hover {

    background:#146c43;

}

</style>

</head>


<body>


<div class="container py-5">


<!-- ==================================================
     PAGE TITLE
================================================== -->

<div class="text-center mb-4">

    <h1 class="checkout-title">

        Checkout

    </h1>

    <p class="text-muted">

        Review your order and select payment method

    </p>

</div>


<div class="row g-4">


<!-- ==================================================
     CUSTOMER DETAILS
================================================== -->

<div class="col-md-5">

    <div class="card shadow customer-card">


        <div class="card-header bg-primary text-white">

            Customer Details

        </div>


        <div class="card-body">


            <p>

                <strong>Name:</strong><br>

                <?php
                echo htmlspecialchars(
                    $customer['name']
                );
                ?>

            </p>


            <p>

                <strong>Email:</strong><br>

                <?php
                echo htmlspecialchars(
                    $customer['email']
                );
                ?>

            </p>


            <p>

                <strong>Mobile:</strong><br>

                <?php
                echo htmlspecialchars(
                    $customer['mobile_no']
                );
                ?>

            </p>


            <p>

                <strong>Address:</strong><br>

                <?php
                echo nl2br(
                    htmlspecialchars(
                        $customer['address']
                    )
                );
                ?>

            </p>


        </div>

    </div>


    <!-- BACK BUTTON -->

    <div class="mt-3">

        <a
        href="cart.php"
        class="btn btn-secondary">

            ← Back to Cart

        </a>

    </div>

</div>



<!-- ==================================================
     ORDER SUMMARY
================================================== -->

<div class="col-md-7">


    <div class="card shadow order-card">


        <div class="card-header bg-success text-white">

            Order Summary

        </div>


        <div class="card-body">


            <div class="table-responsive">


                <table class="table table-bordered align-middle">


                    <thead class="table-dark">

                    <tr>

                        <th>Sweet</th>

                        <th>Price</th>

                        <th>Qty</th>

                        <th>Subtotal</th>

                    </tr>

                    </thead>


                    <tbody>


                    <?php foreach ($cartItems as $item) { ?>


                        <tr>


                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $item['sweet_name']
                                );

                                ?>

                            </td>


                            <td>

                                ₹<?php

                                echo number_format(
                                    $item['price'],
                                    2
                                );

                                ?>

                            </td>


                            <td>

                                <?php

                                echo $item['quantity'];

                                ?>

                            </td>


                            <td>

                                ₹<?php

                                $subtotal =
                                    $item['price']
                                    * $item['quantity'];

                                echo number_format(
                                    $subtotal,
                                    2
                                );

                                ?>

                            </td>


                        </tr>


                    <?php } ?>


                    </tbody>


                    <tfoot>


                    <tr>

                        <th
                        colspan="3"
                        class="text-end">

                            Grand Total

                        </th>


                        <th>

                            ₹<?php

                            echo number_format(
                                $grandTotal,
                                2
                            );

                            ?>

                        </th>

                    </tr>


                    </tfoot>


                </table>


            </div>


            <!-- ==================================================
                 PAYMENT FORM
            ================================================== -->
<form
action="./place_order.php"
method="POST">


                <!-- Total amount -->

                <input
                type="hidden"
                name="total_amount"
                value="<?php
                echo htmlspecialchars(
                    $grandTotal
                );
                ?>">


                <div class="payment-box mt-4">


                    <h5 class="mb-3">

                        Payment Method

                    </h5>


                    <select
                    name="payment_method"
                    class="form-select form-select-lg"
                    required>


                        <option value="">

                            -- Select Payment Method --

                        </option>


                        <option
                        value="Cash on Delivery">

                            Cash on Delivery

                        </option>


                        <option
                        value="UPI">

                            UPI

                        </option>


                        <option
                        value="Credit Card">

                            Credit Card

                        </option>


                        <option
                        value="Debit Card">

                            Debit Card

                        </option>


                    </select>


                </div>


                <!-- ==================================================
                     PLACE ORDER BUTTON
                ================================================== -->

                <div class="d-grid mt-4">


                 <button
type="submit"
name="place_order"
class="btn place-order-btn">
🛒 Place Order
</button>

                </div>


            </form>


        </div>


    </div>


    <!-- CONTINUE SHOPPING -->

    <div class="text-end mt-3">

        <a
        href="sweets.php"
        class="btn btn-warning">

            Continue Shopping

        </a>

    </div>


</div>


</div>


</div>


<script
src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>


</body>

</html>