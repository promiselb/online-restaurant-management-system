<?php
session_start();
require "functions.php";

// Check if user is admin
if (!isset($_SESSION['accountType']) || $_SESSION['accountType'] !== 'Admin') {
    // header("Location: login.php");
    echo "❌ You do not have permission to access this page.";
    exit;
}

$conn = getPDOConnection();
if (!$conn) {
    die("❌ Database connection failed.");
}

// Check if ID parameter exists
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("❌ Invalid product ID.");
}

$productId = (int)$_GET['id'];

try {
    // First delete from OrderProduct to maintain referential integrity
    $conn->beginTransaction();
    
    // Delete product references from OrderProduct table
    $stmt = $conn->prepare("DELETE FROM OrderProduct WHERE ProductId = :id");
    $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
    $stmt->execute();

    // Then delete the product itself
    $stmt = $conn->prepare("DELETE FROM Product WHERE Id = :id");
    $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
    $stmt->execute();

    $conn->commit();

    if ($stmt->rowCount() > 0) {
        echo "✅ Product deleted successfully!";
        // Optional: Redirect back to products page
        header("Location: manage.php");
        
    } else {
        echo "⚠️ No product found with that ID.";
    }
} catch (PDOException $e) {
    $conn->rollBack();
    die("❌ Error deleting product: " . $e->getMessage());
}
?>