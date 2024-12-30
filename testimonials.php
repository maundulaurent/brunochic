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
        <h2>TESTIMONIALS</h2>
        <p>Necessitatibus eius consequatur</p>
      </div><!-- End Section Title -->

      <div class="testimonial-wrap">
        <div class="container">
          <div class="row">
            <div class="col-md-6 mb-4 mb-md-4">
              <div class="testimonial">
                <img src="assets/img/testimonials/testimonials-1.jpg" alt="Testimonial author">
                <blockquote>
                  <p>
                    “Lorem ipsum dolor sit, amet consectetur adipisicing
                    elit. Provident deleniti iusto molestias, dolore vel fugiat
                    ab placeat ea?”
                  </p>
                </blockquote>
                <p class="client-name">James Smith</p>
              </div>
            </div>
            <div class="col-md-6 mb-4 mb-md-4">
              <div class="testimonial">
                <img src="assets/img/testimonials/testimonials-2.jpg" alt="Testimonial author">
                <blockquote>
                  <p>
                    “Lorem ipsum dolor sit, amet consectetur adipisicing
                    elit. Provident deleniti iusto molestias, dolore vel fugiat
                    ab placeat ea?”
                  </p>
                </blockquote>
                <p class="client-name">Kate Smith</p>
              </div>
            </div>
            <div class="col-md-6 mb-4 mb-md-4">
              <div class="testimonial">
                <img src="assets/img/testimonials/testimonials-3.jpg" alt="Testimonial author">
                <blockquote>
                  <p>
                    “Lorem ipsum dolor sit, amet consectetur adipisicing
                    elit. Provident deleniti iusto molestias, dolore vel fugiat
                    ab placeat ea?”
                  </p>
                </blockquote>
                <p class="client-name">Claire Anderson</p>
              </div>
            </div>
            <div class="col-md-6 mb-4 mb-md-4">
              <div class="testimonial">
                <img src="assets/img/testimonials/testimonials-4.jpg" alt="Testimonial author">
                <blockquote>
                  <p>
                    “Lorem ipsum dolor sit, amet consectetur adipisicing
                    elit. Provident deleniti iusto molestias, dolore vel fugiat
                    ab placeat ea?”
                  </p>
                </blockquote>
                <p class="client-name">Dan Smith</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section><!-- /Testimonials Section -->

    <!-- Call To Action Section -->
    <section id="call-to-action" class="call-to-action section light-background">

      <div class="content">
        <div class="container">
          <div class="row align-items-center">
            <div class="col-lg-6">
              <h3>Subscribe To Our Newsletter</h3>
              <p class="opacity-50">
                Lorem ipsum dolor sit amet consectetur adipisicing elit.
                Nesciunt, reprehenderit!
              </p>
            </div>
            <div class="col-lg-6">
              <form action="forms/newsletter.php" class="form-subscribe php-email-form">
                <div class="form-group d-flex align-items-stretch">
                  <input type="email" name="email" class="form-control h-100" placeholder="Enter your e-mail">
                  <input type="submit" class="btn btn-secondary px-4" value="Subcribe">
                </div>
                <div class="loading">Loading</div>
                <div class="error-message"></div>
                <div class="sent-message">
                  Your subscription request has been sent. Thank you!
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section><!-- /Call To Action Section -->

  </main>

  <?php include_once "includes/footer.php" ?>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <?php include_once "includes/scripts.php" ?>

</body>

</html>