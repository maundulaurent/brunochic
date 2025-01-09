<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Testimonials - AgriCulture Bootstrap Template</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <?php include_once "includes/links.php" ?>

</head>

<body class="testimonials-page">

<?php include_once "includes/navbar.php" ?>

  <main class="main">

    <!-- Page Title -->
    <div class="page-title dark-background" data-aos="fade" style="background-image: url(assets/img/page-title-bg.webp);">
      <div class="container position-relative">
        <h1>Testimonials</h1>
        <p>
          Home
          /
          Testimonials
        </p>
        <nav class="breadcrumbs">
          <ol>
            <li><a href="index">Home</a></li>
            <li class="current">Testimonials</li>
          </ol>
        </nav>
      </div>
    </div><!-- End Page Title -->

    <!-- Testimonials Section -->
    <section class="testimonials-12 testimonials section" id="testimonials">
      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <p>Cart</p>
      </div><!-- End Section Title -->
<style>
    .checkout-container {
        display: flex;
        padding: 20px;
        width: 90%;
        border-radius: 8px;
        text-align: center;
        outline: 1px solid #222;
        justify-content: space-between;
    }
    .check-item {
        margin-right: 18px;
    }
    .check-item small{
        justify-content: center;
        align-items: center;
    }
    .bi-x-circle:hover{
        font-size: 33px;
        /* background-color: #f0f0f0; */
    }
</style>
        <div class="row mb-2 " style="display: flex;">
            
            <div class="container checkout-container">
                <div class="check-item">
                    <h5>Product</h5>
                    <small>Layers Chicks</small>
                </div>
                <div class="check-item">
                    <h5>Price</h5>
                    <small>Ksh. 120</small>
                </div>
                <div class="check-item">
                    <h5>Quantity</h5>
                    <small class="d-flex" style="outline: 1px solid #222; border-radius: 18px;">
                        <p class="pt-2 me-4">12</p>
                        <p class="pt-2 me-2"><i class="bi bi-dash-lg"></i></p>
                        <p class="pt-2 "><i class="bi bi-plus-lg"></i></p>
                    </small>
                </div>
                <div class="check-item">
                    <h5>Total Price</h5>
                    <small>Ksh. 1200</small>
                </div>
                <div class="" style="font-size: 30px;">
                    <i class="bi bi-x-circle"></i>
                </div>
            </div> 
            
        </div>
        
    </section><!-- /Testimonials Section -->

  </main>

  <?php include_once "includes/footer.php" ?>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <?php include_once "includes/scripts.php" ?>

</body>

</html>