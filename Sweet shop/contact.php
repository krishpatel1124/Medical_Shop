<?php
session_start();
include("config/db.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Contact Us - Krishna Sweets</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body {
    background-color: #f8f9fa;
    font-family: Arial, sans-serif;
}

/* Navbar */

.navbar-brand {
    font-weight: bold;
}

/* Contact Section */

.contact-box {
    background: white;
    padding: 40px;
    margin-top: 50px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}

/* Heading */

.contact-title {
    color: #dc3545;
    font-weight: bold;
}

/* Contact Information */

.info-box {
    background: #f8f9fa;
    border: 1px solid #ddd;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.info-box h5 {
    color: #dc3545;
    font-weight: bold;
}

/* Button */

.contact-btn {
    background-color: #dc3545;
    color: white;
}

.contact-btn:hover {
    background-color: #bb2d3b;
    color: white;
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
class="nav-link"
href="about.php">

About

</a>

</li>


<li class="nav-item">

<a
class="nav-link active"
href="contact.php">

Contact

</a>

</li>

</ul>

</div>

</div>

</nav>


<!-- ================= CONTACT ================= -->

<div class="container">

<div class="contact-box">

<h1 class="text-center contact-title mb-4">

Contact Us

</h1>

<p class="text-center text-muted mb-5">

Have any questions or need help with your order?
Contact Krishna Sweets.

</p>


<div class="row">


<!-- ================= CONTACT INFORMATION ================= -->

<div class="col-md-5">

<h3 class="mb-4">

Get In Touch

</h3>


<div class="info-box">

<h5>
Address
</h5>

<p class="mb-0 text-muted">

Krishna Sweets<br>
Main Market Road<br>
Gujarat, India

</p>

</div>


<div class="info-box">

<h5>
Phone
</h5>

<p class="mb-0 text-muted">

+91 7383757333

</p>

</div>


<div class="info-box">

<h5>
Email
</h5>

<p class="mb-0 text-muted">

krishnasweets13@gmail.com

</p>

</div>


<div class="info-box">

<h5>
Opening Hours
</h5>

<p class="mb-0 text-muted">

Monday - Sunday<br>
9:00 AM - 9:00 PM

</p>

</div>

</div>


<!-- ================= CONTACT FORM ================= -->

<div class="col-md-7">

<h3 class="mb-4">

Send Us a Message

</h3>


<form method="POST" action="">


<div class="mb-3">

<label class="form-label">
Your Name
</label>

<input
type="text"
name="name"
class="form-control"
placeholder="Enter your name"
required>

</div>


<div class="mb-3">

<label class="form-label">
Email
</label>

<input
type="email"
name="email"
class="form-control"
placeholder="Enter your email"
required>

</div>


<div class="mb-3">

<label class="form-label">
Mobile Number
</label>

<input
type="text"
name="mobile"
class="form-control"
placeholder="Enter your mobile number"
required>

</div>


<div class="mb-3">

<label class="form-label">
Message
</label>

<textarea
name="message"
class="form-control"
rows="5"
placeholder="Enter your message"
required></textarea>

</div>


<button
type="submit"
name="send"
class="btn contact-btn">

Send Message

</button>


</form>

</div>

</div>

</div>

</div>


<!-- ================= FOOTER ================= -->

<footer>

<p class="mb-1">

<strong>Krishna Sweets</strong>

</p>

<p class="mb-0">

Delicious sweets for every occasion.

</p>

<p class="mt-2 mb-0">

© <?php echo date("Y"); ?>
Krishna Sweets. All Rights Reserved.

</p>

</footer>


<script
src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

</body>

</html>