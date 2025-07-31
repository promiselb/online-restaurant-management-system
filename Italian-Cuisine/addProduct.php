<?php
    session_start();
    require "functions.php";

    // Check if user is admin
    if (!isset($_SESSION['accountType']) || $_SESSION['accountType'] !== 'Admin') {
        header("Location: login.php");
        exit;
    }

    $conn = getPDOConnection();
    if (!$conn) {
        die("❌ Database connection failed.");
    }


    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validate input
        $required = ['name', 'description', 'price', 'categorieId'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                die("❌ All fields are required.");
            }
        }

        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $price = (float)$_POST['price'];
        $categorieId = (int)$_POST['categorieId'];

        if ($price <= 0 ) {
            die("❌ Price must be positive and quantity cannot be negative.");
        }

        try {
            // Get max product ID first
            $maxId = $conn->query("SELECT MAX(Id) FROM Product")->fetchColumn();
            $newId = $maxId ? $maxId + 1 : 1;

            // Insert new product with explicit ID
            $stmt = $conn->prepare("INSERT INTO Product 
                                  (Id, Name, Description, Price, CategorieId) 
                                  VALUES (:id, :name, :description, :price, :categorieId)");
            $stmt->bindParam(':id', $newId, PDO::PARAM_INT);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':categorieId', $categorieId);
            $stmt->execute();

            echo "✅ Product added successfully! ID: " . $newId;
            header("Location: manage.php"); // Uncomment to redirect after adding
        } catch (PDOException $e) {
            die("❌ Error adding product: " . $e->getMessage());
        }
    }
?>