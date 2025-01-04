<?php
session_start();
require_once '../../../includes/config.php';

if(isset($_POST['submit'])) {
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
        $_SESSION['error'] = "Error adding chick product: " . $e->getMessage();
    }
    
    header("Location: add-chicks.php");
    exit();
}