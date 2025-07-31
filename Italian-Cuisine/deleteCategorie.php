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
    die("❌ Invalid Categorie ID.");
}

$CategorieId = (int)$_GET['id'];

try {
    // Prepare and execute delete query
    $stmt = $conn->prepare("DELETE FROM Categorie WHERE Id = :id");
    $stmt->bindParam(':id', $CategorieId, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo "✅ Categorie deleted successfully!";
        // Optional: Redirect back to categories page
        header("Location: manage.php"); // Uncomment to redirect after adding
    } else {
        echo "⚠️ No Categorie found with that ID.";
    }
} catch (PDOException $e) {
    die("❌ Error deleting Categorie: " . $e->getMessage());
}
?>