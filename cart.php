<?php
session_start();

require_once 'includes/config.php';


try {
    // Fetch items from the cart table
    $query = "SELECT * FROM cart";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['error'] = "Error fetching cart items: " . $e->getMessage();
    $cart_items = [];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Machcom - cart</title>
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

</head>

<body class="testimonials-page">

<?php include_once "includes/navbar.php" ?>

  <main class="main">

    <!-- Page Title -->
    <div class="page-title dark-background" data-aos="fade" style="background-image: url(assets/img/page-title-bg.webp);">
      <div class="container position-relative">
        <h1>Cart</h1>
        
        <nav class="breadcrumbs">
          <ol>
            <li><a href="index">Home</a></li>
            <li class="current">cart</li>
          </ol>
        </nav>
      </div>
    </div><!-- End Page Title -->

    <!-- Testimonials Section -->
    <section class="testimonials-12 testimonials section" id="testimonials">
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
      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <p> Your Cart</p>
      </div><!-- End Section Title -->


<?php if (empty($cart_items)): ?>
        <!-- when no items are in the cart -->
         <h5 class="container">No items in cart || <a href="shop" class="text-decoration-underline">Continue Shopping</a></h5>
<?php else: ?>
    <?php foreach ($cart_items as $item): ?>
        <div class="row mb-2 " style="display: flex;">
            <div class="container checkout-container">
                <div class="check-item">
                    <h5>Product</h5>
                    <small><?php echo htmlspecialchars($item['product']); ?></small>
                </div>
                <div class="check-item">
                    <h5>Price</h5>
                    <small>Ksh. <?php echo number_format($item['price'], 2); ?></small>
                </div>
                <div class="check-item">
                    <h5>Quantity</h5>
                    <small class="d-flex" style="outline: 1px solid #222; border-radius: 18px;">
                        <p class="pt-2 me-4"><?php echo htmlspecialchars($item['quantity']); ?></p>
                        <p class="pt-2 me-2"><i class="bi bi-dash-lg"></i></p>
                        <p class="pt-2 "><i class="bi bi-plus-lg"></i></p>
                    </small>
                </div>
                <div class="check-item">
                    <h5>Total Price</h5>
                    <small>Ksh. <?php echo number_format($item['total_price'], 2); ?></small>
                </div>
                <div class="" style="font-size: 30px;">
                <a href="remove-from-cart.php?id=<?php echo $item['id']; ?>" class="text-danger">
                    <i class="bi bi-x-circle"></i>
                </a>
                </div>
            </div> 
            
        </div>
        <?php endforeach; ?>
        <div style="text-align: right; margin-top: 15px;">
            <div class="container">
                <a href="checkout" class="btn btn-primary " >Check Out Items</a>
            </div>
        </div>
<?php endif; ?>
        
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