<?php
session_start();
require_once '../../../includes/config.php';

// Fetch categories
try {
  $query = "SELECT id, category_name FROM categories";
  $categories_stmt = $pdo->prepare($query);
  $categories_stmt->execute();
  $categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
  $_SESSION['error'] = "Error fetching categories: " . $e->getMessage();
  $categories = [];
}


if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    try {
        $product_name = $_POST['product_name'];
        $description = $_POST['description'];
        $category = $_POST['category'];
        $price = floatval($_POST['price']);
        $stock = intval($_POST['stock']);
        
        // Handle file upload
        $image_path = '';
        if(isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
            $upload_dir = '../../uploads/chicks/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION);
            $file_name = uniqid() . '.' . $file_extension;
            $target_path = $upload_dir . $file_name;
            
            if(move_uploaded_file($_FILES['product_image']['tmp_name'], $target_path)) {
                $image_path = 'uploads/chicks/' . $file_name;
            }
        }
        
        $query = "INSERT INTO chicks (product_name, description, category, price, stock_quantity, image_path) 
                  VALUES (:product_name, :description, :category, :price, :stock, :image_path)";
                  
        $stmt = $pdo->prepare($query);
        
        $stmt->execute([
            ':product_name' => $product_name,
            ':description' => $description,
            ':category' => $category,
            ':price' => $price,
            ':stock' => $stock,
            ':image_path' => $image_path
        ]);
        
        $_SESSION['success'] = "Chick product added successfully!";
        
    } catch(PDOException $e) {
        $_SESSION['error'] = "Error adding chick item: " . $e->getMessage();
    }
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Machcom Devs | Admin</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
  <!-- daterange picker -->
  <link rel="stylesheet" href="../../plugins/daterangepicker/daterangepicker.css">
  <!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="../../plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Bootstrap Color Picker -->
  <link rel="stylesheet" href="../../plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="../../plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- Select2 -->
  <link rel="stylesheet" href="../../plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="../../plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
  <!-- Bootstrap4 Duallistbox -->
  <link rel="stylesheet" href="../../plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css">
  <!-- BS Stepper -->
  <link rel="stylesheet" href="../../plugins/bs-stepper/css/bs-stepper.min.css">
  <!-- dropzonejs -->
  <link rel="stylesheet" href="../../plugins/dropzone/min/dropzone.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../../dist/css/adminlte.min.css">

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
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <!-- Navbar -->
  <?php include "../../includes/navbar.php" ?>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <?php include "../../includes/sidebar.php" ?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Add Chicks</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active">Add Chicks</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
  <?php
  if(isset($_SESSION['success'])) {
    echo '<div class="alert alert-success  alert-dismissible">
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
      <div class="container-fluid">
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
          <!-- SELECT2 EXAMPLE -->
          <div class="card card-default">
            <div class="card-header">
              <h3 class="card-title">Basic Information Form</h3>

              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fas fa-minus"></i>
                </button>
              </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
            <div class="form-group">
              <label for="product_name">Product Name</label>
              <input type="text" class="form-control" name="product_name" id="product_name" required placeholder="Enter product Name">
            </div>
            <div class="form-group">
              <label for="description">Description</label>
              <input type="text" class="form-control" name="description" id="description" required placeholder="Enter product description">
              
            </div>                
            <div class="form-group">
              <label>Product Category</label>
              <select class="form-control select2" name="category" style="width: 100%;" required>
              <option value="" disabled selected>Select Category</option>
                <?php foreach ($categories as $category): ?>
                  <option value="<?php echo htmlspecialchars($category['id']); ?>">
                    <?php echo htmlspecialchars($category['category_name']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="price">Price per Product</label>
              <input type="number" step="0.01" class="form-control" name="price" id="price" required placeholder="Enter price">
            </div>
            <div class="form-group">
              <label for="stock">Number in stock</label>
              <input type="number" class="form-control" name="stock" id="stock" required placeholder="Enter stock quantity">
            </div>
            <div class="form-group">
              <label for="product_image">Product image</label>
              <div class="input-group">
                <div class="custom-file">
                  <input type="file" class="custom-file-input"  name="product_image" id="product_image" required>
                  <label class="custom-file-label" for="product_image">Choose image</label>
                </div>
              </div>
            </div>
            <div class="">
              <button type="submit" name="submit" class="btn btn-success">Save</button>
            </div>
          </div>
              <!-- /.card -->
                </div>
              </div>
              <!-- /.row -->            
            </div>          
            <!-- /.card-body -->
            
          </div>
          <!-- /.card -->

        </div>
      </form>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <?php include "../../includes/footer.php" ?>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="../../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- Select2 -->
<script src="../../plugins/select2/js/select2.full.min.js"></script>
<!-- Bootstrap4 Duallistbox -->
<script src="../../plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js"></script>
<!-- InputMask -->
<script src="../../plugins/moment/moment.min.js"></script>
<script src="../../plugins/inputmask/jquery.inputmask.min.js"></script>
<!-- date-range-picker -->
<script src="../../plugins/daterangepicker/daterangepicker.js"></script>
<!-- bootstrap color picker -->
<script src="../../plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="../../plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Bootstrap Switch -->
<script src="../../plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<!-- BS-Stepper -->
<script src="../../plugins/bs-stepper/js/bs-stepper.min.js"></script>
<!-- dropzonejs -->
<script src="../../plugins/dropzone/min/dropzone.min.js"></script>
<!-- AdminLTE App -->
<script src="../../dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../../dist/js/demo.js"></script>
<!-- Page specific script -->


</body>
</html>
