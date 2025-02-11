
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Machcom - CheckOut</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <?php include_once "includes/links.php" ?>

  <style>
    .alert {
        position: fixed; /* Make the alert float */
        top: 80px; /* Distance from the top */
        right: 10px; /* Distance from the right */
        width: auto; /* Allow adjustable width */
        max-width: 400px; /* Set a maximum width for larger screens */
        z-index: 1000; /* Ensure the alert is above other content */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2); /* Add a subtle shadow */
        border-radius: 5px; /* Rounded corners */
        font-size: 14px; /* Adjust font size */
    }

    .alert-success {
        background-color: #d4edda; /* Light green background */
        color: #155724; /* Dark green text */
        border: 1px solid #c3e6cb; /* Border color */
    }
    .alert-danger {
        background-color: #f8d7da; /* Light red background */
        color: #721c24; /* Dark red text */
        border: 1px solid #f5c6cb; /* Border color */
    }
    
</style>

</head>

<body class="testimonials-page">

<?php include_once "includes/navbar.php" ?>

<main class="main">

    <!-- Page Title -->
    <div class="page-title dark-background" data-aos="fade" style="background-image: url(assets/img/page-title-bg.webp);">
      <div class="container position-relative">
        <h1>Check Out</h1>
        
        <nav class="breadcrumbs">
          <ol>
            <li><a href="index">Home</a></li>
            <li class="current">checkout</li>
          </ol>
        </nav>
      </div>
    </div><!-- End Page Title -->



<!-- Contact Section -->
<section id="contact" class="contact section">
    

<?php
    if(isset($_SESSION['success'])) {
    echo '<div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <h5><i class="icon fas fa-check"></i> Success!</h5>
        '.$_SESSION['success'].'
    </div>';
    unset($_SESSION['success']);
    }
    if(isset($_SESSION['error'])) {
        echo '<div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <h5><i class="icon fas fa-ban"></i> Error!</h5>
        '.$_SESSION['error'].'
        </div>';
        unset($_SESSION['error']);
}
?>

    <div class="container" data-aos="fade">
    <h2 class="text-center py-5">Check Out</h2>
    <div class="row gy-5 gx-lg-5">

        <div class="col-lg-4">

        <div class="info">
            <h3>Your Order</h3>
            <!-- <p>Et id eius voluptates atque nihil voluptatem enim in tempore minima sit ad mollitia commodi minus.</p> -->

            <div class="info-item d-flex">
            <i class="bi bi-geo-alt flex-shrink-0"></i>
            <div>
                <h4>Products</h4>
                <div>
                    <p class="mr-5">OnePlus Warp Broiler Chicks - </p>
                    <small >Ksh: 120</small>
                </div>
                <div>
                    <p class="mr-5">OnePlus Warp Broiler Chicks - </p>
                    <small >Ksh: 120</small>
                </div>
            </div>
            </div><!-- End Info Item -->

            <div class="info-item d-flex">
            <i class="bi bi-envelope flex-shrink-0"></i>
            <div>
                <h4>Total:</h4>
                <p>Ksh. 1200</p>
            </div>
            </div><!-- End Info Item -->

            

        </div>

        </div>

        <div class="col-lg-8">
            <h3 class="mb-4">Shipping and payment details</h3>
        <form action="forms/contact.php" method="post" role="form" class="php-email-form">
            <div class="row">
            <div class="col-md-6 form-group">
                <label for="name">First Name *</label>
                <input type="text" name="name" class="form-control" id="name" placeholder="John" required="">
            </div>
            <div class="col-md-6 form-group mt-3 mt-md-0">
                <label for="last_name">Last Name *</label>
                <input type="text" class="form-control" name="last_name" id="last_name" placeholder="Doe" required="">
            </div>
            <div class="col-md-6 form-group mt-3 mt-md-0">
                <label for="email">Email Address</label>
                <input type="email" class="form-control" name="email" id="email" placeholder="johndoe@gmail.com" >
            </div>
            <div class="col-md-6 form-group mt-3 mt-md-0">
                <label for="phone">Phone Number *</label>
                <input type="text" class="form-control" name="phone" id="phone" placeholder="254123456789" required="">
            </div>
            <div class="col-md-6 form-group mt-3 mt-md-0">
                <label for="county">County *</label>
                <input type="text" class="form-control" name="county" id="county" placeholder="eg, Nairobi" required="">
            </div>
            <div class="col-md-6 form-group mt-3 mt-md-0">
                <label for="region">Region *</label>
                <input type="text" class="form-control" name="region" id="region" placeholder="Your Region" required="">
            </div>
            </div>
            <div class="form-group mt-3">
            <label for="describe_area">Exact Area (Describe) *</label>
            <input type="text" class="form-control" name="describe_area" id="describe_area" placeholder="Describe in Details" required="">
            </div>
            <div class="form-group mt-3">
            <label for="notes">Order notes (optional)</label>
            <textarea class="form-control" name="notes" placeholder="Order Notes" ></textarea>
            </div>
            <div class="my-3">
            </div>
            <div class="text-center"><button type="submit">Place Order</button></div>
        </form>
        </div><!-- End Contact Form -->

    </div>

    </div>

</section><!-- /Contact Section -->

  </main>

  <?php include_once "includes/footer.php" ?>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <?php include_once "includes/scripts.php" ?>

</body>

</html>