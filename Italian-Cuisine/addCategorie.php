<?php
    session_start();
    require "functions.php";   

    // Check if user is admin
    if (!isset($_SESSION['accountType']) || $_SESSION['accountType'] !== 'Admin') {
        echo "❌ You do not have permission to access this page.";
        exit;
    }
    
    $conn = getPDOConnection();
    if (!$conn) {
        die("❌ Database connection failed.");
    }

    // Get category name from POST
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    
    if (empty($name)) {
        die("❌ Category name cannot be empty.");
    }

    try {
        // Check if category already exists
        $stmt = $conn->prepare("SELECT Id FROM Categorie WHERE Name = :name");
        $stmt->bindParam(':name', $name);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            die("❌ A category with this name already exists.");
        }

        // Get maximum existing ID
        $maxId = $conn->query("SELECT MAX(Id) FROM Categorie")->fetchColumn();
        $newId = $maxId ? $maxId + 1 : 1;  // Start with 1 if no categories exist

        // Insert new category with explicit ID
        $stmt = $conn->prepare("INSERT INTO Categorie (Id, Name) VALUES (:id, :name)");
        $stmt->bindParam(':id', $newId, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name);
        $stmt->execute();

        echo "✅ Category added successfully with ID: " . $newId;
        header("Location: manage.php"); // Uncomment to redirect after adding
    } catch (PDOException $e) {
        die("❌ Database error: " . $e->getMessage());
    }
?>