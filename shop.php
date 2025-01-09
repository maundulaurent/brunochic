<?php
session_start();
require_once 'includes/config.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Machcom - Shop</title>
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

<body class="blog-page">
<?php include_once "includes/navbar.php" ?>

  <main class="main">

    <!-- Page Title -->
    <div class="page-title dark-background" data-aos="fade" style="background-image: url(assets/img/page-title-bg.webp);">
      <div class="container position-relative">
        <h1>Shop</h1>
        <nav class="breadcrumbs">
          <ol>
            <li><a href="./">Home</a></li>
            <li class="current">shop</li>
          </ol>
        </nav>
      </div>
    </div><!-- End Page Title -->


<?php

try {
    $query = "
    SELECT chicks.*, categories.category_name 
    FROM chicks
    INNER JOIN categories ON chicks.category = categories.id
  ";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $chicks = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['error'] = "Error fetching chicks: " . $e->getMessage();
    //echo "Error fetching chicks: " . $e->getMessage();
}

?>
    <!-- Blog Posts 2 Section -->
    <section id="blog-posts-2" class="blog-posts-2 section">

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
      <div class="container">
        <div class="row gy-4">
        <h4 class="content-title mb-4">Available now</h4>

        <?php foreach ($chicks as $chick): ?>

          <div class="col-lg-3">
            <article class="position-relative">

              <div class="post-img position-relative overflow-hidden">
                <img src="admin/<?php echo $chick['image_path']; ?>" class="img-fluid" alt="<?php echo $chick['product_name']; ?>">
              </div>

              <div class="meta d-flex align-items-end">
                <span class="post-date"><span><?php echo $chick['price']; ?></span>Ksh</span>
                <div class="d-flex align-items-center">
                  <i class="bi bi-feather"></i> <span class="ps-2"><?php echo $chick['category_name']; ?></span>
                </div>
              </div>

              <div class="post-content d-flex flex-column">

                <h5 class="post-title"><?php echo $chick['product_name']; ?></h5>
                <p class="post-description"><?php echo $chick['description']; ?></p>
                <a href="chick-details?id=<?php echo $chick['id']; ?>" class="readmore stretched-link"></a>
              </div>

            </article>
          </div><!-- End post list item -->

        <?php endforeach; ?>
        </div>
      </div>

    </section><!-- /Blog Posts 2 Section -->

    <!-- Blog Pagination Section -->
    <section id="blog-pagination" class="blog-pagination section">

      <div class="container">
        <div class="d-flex justify-content-center">
          <ul>
            <li><a href="#"><i class="bi bi-chevron-left"></i></a></li>
            <li><a href="#">1</a></li>
            <li><a href="#" class="active">2</a></li>
            <li><a href="#">3</a></li>
            <li><a href="#">4</a></li>
            <li>...</li>
            <li><a href="#">10</a></li>
            <li><a href="#"><i class="bi bi-chevron-right"></i></a></li>
          </ul>
        </div>
      </div>

    </section><!-- /Blog Pagination Section -->

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