<?php
session_start();
require_once 'includes/config.php';

if (!isset($_GET['id'])) {
    $_SESSION['error'] = "No chick ID provided.";
    header("Location: shop");
    exit();
}

$chick_id = intval($_GET['id']);

try {
    // Query to fetch chick details along with its category name
    $query = "
        SELECT chicks.*, categories.category_name 
        FROM chicks 
        INNER JOIN categories ON chicks.category = categories.id
        WHERE chicks.id = :id
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':id' => $chick_id]);
    $chick = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$chick) {
        $_SESSION['error'] = "Chick not found.";
        header("Location: shop.php");
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Error fetching chick details: " . $e->getMessage();
    header("Location: shop.php");
    exit();
}


// The comment Section Below there for the comments
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comments'])) {
  try {
      $name = $_POST['name'];
      $email = $_POST['email'];
      $about = $_POST['about'];
      $comment = $_POST['comment'];
      
      $query = "INSERT INTO comments (name, email, about, comment) 
                VALUES (:name, :email, :about, :comment)";
                
      $stmt = $pdo->prepare($query);
      
      $stmt->execute([
          ':name' => $name,
          ':email' => $email,
          ':about' => $about,
          ':comment' => $comment
      ]);
      
      $_SESSION['success'] = "Comment sent successfully!";
      
  } catch(PDOException $e) {
      $_SESSION['error'] = "Error sending comment!: " . $e->getMessage();
  }

   // Ensure 'id' is present in the URL before redirecting
   if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $redirect_url = $_SERVER['PHP_SELF'] . "?id=" . intval($_GET['id']);
    } else {
        $redirect_url = "shop.php"; // Fallback to shop page if ID is missing
    }
  
  header("Location: $redirect_url ");
  exit();
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Product Details - Machcom</title>
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

    /* Order Modal */
    /* Modal Styles */
  .modal {
    display: none;  /* Hidden by default */
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent background */
  }

  .modal-content {
    background-color: #fff;
    margin: 10% auto;
    padding: 10px;
    border-radius: 5px;
    width: 80%;
    max-width: 700px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    position: relative;
    text-align: center;
  }

  .close-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    font-size: 20px;
    color: #000;
    cursor: pointer;
  }

</style>

</head>

<body class="blog-details-page">
<?php include_once "includes/navbar.php" ?>

  <main class="main">

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

    <!-- Page Title -->
    <div class="page-title dark-background" data-aos="fade" style="background-image: url(assets/img/page-title-bg.webp);">
      <div class="container position-relative">
        <h1>Chick Details</h1>
        <p>Discover all you need to know about this breed of chick, from its growth rate to optimal care tips.</p>
        <nav class="breadcrumbs">
          <ol>
            <li><a href="index">Home</a></li>
            <li class="current">Chick Details</li>
          </ol>
        </nav>
      </div>
    </div><!-- End Page Title -->

    <div class="container">
      <div class="row">

        <div class="col-lg-8">

          <!-- Blog Details Section -->
          <section id="blog-details" class="blog-details section">
            <div class="container">

              <article class="article">

                <div class="post-img">
                  <img src="assets/img/blog/blog-1.jpg" alt="" class="img-fluid">
                </div>

                <h2 class="title"><?php echo(htmlspecialchars($chick['product_name'])); ?></h2>

                <div class="meta-top">
                  <ul>
                  <li><i class="bi bi-calendar"></i> Added on: <time datetime="2025-01-06">Jan 6, 2025</time></li>
                  <li><i class="bi bi-box"></i> Available Stock: 500</li>
                  <li><i class="bi bi-truck"></i> <a href="" id="order-modal-btn" class="text-decoration-underline ">Add to Cart</a></li>
                  
                  </ul>
                </div><!-- End meta top -->

                <div class="content">
                  <p>
                  Broiler chicks are specifically bred for meat production. They grow rapidly and are ready for the market in just 6-8 weeks. This breed is known for its excellent feed conversion ratio and high yield.
                  </p>
                  <blockquote>
                    <p>
                      Et vero doloremque tempore voluptatem ratione vel aut. Deleniti sunt animi aut. Aut eos aliquam doloribus minus autem quos.
                    </p>
                  </blockquote>
                  <h3>More details.</h3> 
                  
                  <p>
                    Rerum ea est assumenda pariatur quasi et quam. Facilis nam porro amet nostrum. In assumenda quia quae a id praesentium. Quos deleniti libero sed occaecati aut porro autem. Consectetur sed excepturi sint non placeat quia repellat incidunt labore. Autem facilis hic dolorum dolores vel.
                    Consectetur quasi id et optio praesentium aut asperiores eaque aut. Explicabo omnis quibusdam esse. Ex libero illum iusto totam et ut aut blanditiis. Veritatis numquam ut illum ut a quam vitae.
                  </p>
                  <p>
                    Alias quia non aliquid. Eos et ea velit. Voluptatem maxime enim omnis ipsa voluptas incidunt. Nulla sit eaque mollitia nisi asperiores est veniam.
                  </p>

                </div><!-- End post content -->

                <div class="meta-bottom">
                  <i class="bi bi-folder"></i>
                  <ul class="cats">
                    <li><a href="#">Business</a></li>
                  </ul>

                  <i class="bi bi-tags"></i>
                  <ul class="tags">
                    <li><a href="#">Creative</a></li>
                    <li><a href="#">Tips</a></li>
                    <li><a href="#">Place an order</a></li>
                  </ul>
                </div><!-- End meta bottom -->

              </article>

            </div>
          </section><!-- /Blog Details Section -->

          <!-- Comment Form Section -->
          <section id="comment-form" class="comment-form section">
            <div class="container">

              <form action="" method="POST">

                <h4>Post Comment</h4>
                <p>Your email address will not be published. Required fields are marked * </p>
                <div class="row">
                  <div class="col-md-6 form-group">
                    <input name="name" type="text" class="form-control" placeholder="Your Name* eg; John Doe">
                  </div>
                  <div class="col-md-6 form-group">
                    <input name="email" type="text" class="form-control" placeholder="Your Email* eg; JohnDoe@main.com">
                  </div>
                </div>
                <div class="row">
                  <div class="col form-group">
                    <input name="about" type="text" class="form-control" placeholder="About eg; I am looking for a certain product...">
                  </div>
                </div>
                <div class="row">
                  <div class="col form-group">
                    <textarea name="comment" class="form-control" placeholder="Your Comment* This is my comment or query.."></textarea>
                  </div>
                </div>

                <div class="text-center">
                  <button type="submit" name="comments" id="comments" class="btn btn-primary">Send</button>
                </div>

              </form>

            </div>
          </section><!-- /Comment Form Section -->

        </div>

        <div class="col-lg-4 sidebar">

          <div class="widgets-container">

            <!-- Blog Author Widget -->
            <div class="blog-author-widget widget-item">

              <div class="d-flex flex-column align-items-center">
                <div class="d-flex align-items-center w-100">
                  <img src="assets/img/blog/blog-author.jpg" class="rounded-circle flex-shrink-0" alt="">
                  <div>
                    <h4>Jane Smith</h4>
                    <div class="social-links">
                      <a href="https://x.com/#"><i class="bi bi-twitter-x"></i></a>
                      <a href="https://facebook.com/#"><i class="bi bi-facebook"></i></a>
                      <a href="https://instagram.com/#"><i class="biu bi-instagram"></i></a>
                      <a href="https://instagram.com/#"><i class="biu bi-linkedin"></i></a>
                    </div>
                  </div>
                </div>

                <p>
                A well-raised broiler chick is the foundation of a successful poultry business." – Expert Farmer 
                </p>

              </div>

            </div><!--/Blog Author Widget -->

            <!-- Search Widget -->
            <div class="search-widget widget-item">

              <h3 class="widget-title">Search</h3>
              <form action="">
                <input type="text">
                <button type="submit" title="Search"><i class="bi bi-search"></i></button>
              </form>

            </div><!--/Search Widget -->

            <!-- Categories Widget -->
            <div class="categories-widget widget-item">

              <h3 class="widget-title">Key Characteristics</h3>
              <ul class="mt-3">
                <li><a href="#">Breed: Broiler </a></li>
                <li><a href="#">Growth Rate: Rapid</a></li>
                <li><a href="#">Optimal Feed: High-protein starter feed</a></li>
                <li><a href="#">Market Weight: 2-3 kg in 6-8 weeks</a></li>
              </ul>

            </div><!--/Categories Widget -->

            <!-- Recent Posts Widget 2 -->
            <div class="recent-posts-widget-2 widget-item">

              <h3 class="widget-title">Care Tips</h3>

              To ensure optimal growth and health:
              <ul>
                <li>Maintain a temperature-controlled brooder environment.</li>
                <li>Provide clean water and feed at all times.</li>
                <li>Vaccinate against common poultry diseases.</li>
                <li>Ensure proper ventilation and hygiene in the coop.</li>
              </ul>

            </div><!--/Recent Posts Widget 2 -->

            <!-- Tags Widget -->
            <div class="tags-widget widget-item">

              <h3 class="widget-title">Tags</h3>
              <ul>
                <li><a href="#">FARMING</a></li>
                <li><a href="#">POULTRY</a></li>
              </ul>

            </div><!--/Tags Widget -->

          </div>

        </div>

      </div>
    </div>

  </main>

  <!-- Ordering Modal -->

  <!-- Modal Structure -->
<div id="orderModal" class="modal">
  <div class="modal-content">
    <span class="close-btn" id="closeModal">&times;</span>
    <h3>Add item to cart</h3>
    <p>Do you want to add <strong><?php echo(htmlspecialchars($chick['product_name'])); ?></strong> into your cart?</p>
    <form action="cart.php" method="POST">
      <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($chick['id']); ?>">
      
      <button type="submit" class="btn btn-primary">Add to cart</button>
    </form>
  </div>
</div>


  <?php include_once "includes/footer.php" ?>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <script>
  // Get modal elements
  const modal = document.getElementById('orderModal');
  const openModalBtn = document.getElementById('order-modal-btn');
  const closeModalBtn = document.getElementById('closeModal');

  // Open modal when "Order Now" is clicked
  openModalBtn.addEventListener('click', function (e) {
    e.preventDefault();
    modal.style.display = 'block';
  });

  // Close modal when close button is clicked
  closeModalBtn.addEventListener('click', function () {
    modal.style.display = 'none';
  });

  // Close modal when clicking outside the modal content
  window.addEventListener('click', function (e) {
    if (e.target === modal) {
      modal.style.display = 'none';
    }
  });

</script>

  <?php include_once "includes/scripts.php" ?>

</body>

</html>