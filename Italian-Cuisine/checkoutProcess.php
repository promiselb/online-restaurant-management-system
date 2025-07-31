<?php
    session_start();
    require 'functions.php';

    // Check if the user is logged in
    if (!isset($_SESSION['gmail'])) {
        // Redirect to login page if not logged in
        header("Location: login.php");
        exit();
    }

    // Check id
    if (!isset($_SESSION['id'])) {
        die("❌ User ID not found in session.");
    } 

    if (!isset($_SESSION['total']) || !is_numeric($_SESSION['total']) || $_SESSION['total'] <= 0) {
        die("❌ Total amount not found or invalid.");
    }

    if (!isset($_SESSION['description'])) {
        die("❌ description not found or invalid.");
    }

    // if method is POST, process the checkout
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $conn = getPDOConnection();
        if (!$conn) {   
            die("Database connection failed.");
        }

        // First get the maximum existing ID
        $stmt = $conn->query("SELECT MAX(Id) FROM `Order`");
        $maxId = $stmt->fetchColumn();
        $newId = $maxId + 1;
        echo "New Order ID: " . $newId . "<br>";
        
        // Then insert with all required columns
        $sql = "INSERT INTO `Order` (Id, Status, Description, Total, ClientId, Date) 
                VALUES (:id, 'Pending', :description, :total, :clientId, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'id' => $newId,
            'description' => $_SESSION['description'],
            'total' => $_SESSION['total'],
            'clientId' => $_SESSION['id'] || null,
        ]);
        echo "Order placed successfully with ID: " . $newId . "<br>";

       
        $orderId = $newId; // get the newly inserted order ID
        echo "Order ID: " . $orderId . "<br>";
        
        // Insert each product into order_product table
        foreach ($_SESSION['cart'] as $productId => $quantity) {
            $sql = "INSERT INTO OrderProduct (OrderId, ProductId, Quantity) 
                    VALUES (:orderId, :productId, :quantity)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                'orderId' => $orderId,
                'productId' => $productId,
                'quantity' => $quantity
            ]);
        }
    }
    
    $_SESSION['total'] = 0.00; // Reset the total in session after placing the order
    $_SESSION['description'] = ""; // Reset the description in session
    $_SESSION['cart'] = []; // Clear the cart after successful order placement
?>