<?php
session_start();
require_once 'includes/config.php';

if (isset($_GET['id'])) {
    try {
        $id = intval($_GET['id']);
        $query = "DELETE FROM cart WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':id' => $id]);
        $_SESSION['success'] = "Item removed from cart.";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error removing item: " . $e->getMessage();
    }
}
header("Location: cart");
exit();
?>
