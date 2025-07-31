<?php
session_start();
require "functions.php";

$conn = getPDOConnection();
if (!$conn) {
    die("❌ Database connection failed.");
}   

// Get product info from menu page (POST)
$product_id = $_POST['product_id'] ?? '';
$product_quantity =  $_POST['product_quantity'] ?? 1; // Default quantity if not set

$product_id = htmlspecialchars($product_id);
$product_quantity = (int) htmlspecialchars($product_quantity);

$_SESSION['cart'] = $_SESSION['cart'] ?? [];
// $_SESSION['cart'] = []; // Uncomment once to clear corrupted cart

// Add product to cart
if ($product_id) {
    if (array_key_exists($product_id, $_SESSION['cart'])) {
        $_SESSION['cart'][$product_id] += $product_quantity;
        
    } else {
        // Add new product with specified quantity
        $_SESSION['cart'][$product_id] = $product_quantity;
    }

    header("Location: " . $_SERVER['PHP_SELF']); // prevents resubmission
    exit;
}

// Remove product from cart
if (isset($_GET['delete'])) {
    $index = (int) $_GET['delete'];
    unset($_SESSION['cart'][$index]);
    unset($_SESSION['cart'][0]);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>My Cart</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Favicons -->
    <link href="img/favicon.ico" rel="icon">
    <link href="img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:200,300,400,500,600|Pacifico" rel="stylesheet"> 

    <!-- CSS Files -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>

<!-- Top Header Start -->
        <section id="top-header">
            <div class="logo">
                <img src="img/logo.png" />
            </div>
        </section>
        <!-- Top Header End -->

      <!-- Header Start -->
       
<header id="header">
    <div class="container">
        <nav id="nav-menu-container">
            <ul class="nav-menu">
                <!-- if account type is client then add a link to order.php -->
                <?php if (isset($_SESSION['accountType']) && $_SESSION['accountType'] === 'Client'): ?>
                    <li><a href="order.php">Order</a></li>
                <?php endif; ?>
                
                <li><a href="profile.php">👥</a></li>
                  <?php if (isset($_SESSION['accountType']) && $_SESSION['accountType'] === 'Admin'): ?>
                    <li><a href="showMessages.php">show Messages</a></li>
                <?php endif; ?>
                <?php if (isset($_SESSION['accountType']) && $_SESSION['accountType'] === 'Admin'): ?>
                    <li><a href="tableReservation.php">Table Reservations</a></li>
                <?php endif; ?>
                <?php if (isset($_SESSION['accountType']) && $_SESSION['accountType'] === 'Admin'): ?>
                    <li><a href="admin.php">Admin</a></li>
                <?php endif; ?>

                <?php if (!isset($_SESSION['gmail'])): ?>
                    <li><a href="login.php">Login</a></li>
                <?php endif; ?>

                <li><a href="index.php">Home</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="menu.php">Menu</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="cart.php">🛒</a></li>
                <?php if (isset($_SESSION['gmail'])): ?>
                    <li><a href="logout.php">Logout</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>

<!-- Main Content -->
<div class="container my-5">
    <h2 class="mb-4 text-center">🛒 Your Shopping Cart</h2>

    <?php if (empty($_SESSION['cart']) || count($_SESSION['cart']) < 0): ?>
        <div class="alert alert-warning text-center">❌ Your cart is currently empty.</div>
    <?php else: ?>
        <div class="row">
            <?php 
                $total = 0.00;
                if (count($_SESSION['cart']) > 0) {

                }
               
                foreach ($_SESSION['cart'] as $productId => $qnn): // $index is the product ID
                    $id = htmlspecialchars($productId);
                    // echo "product id: " . $productId . " | product quantity: " .  $qnn . "<br>";

                    $sql = "SELECT * FROM product WHERE Id = :id";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute(['id' => $id]);
                    $item = $stmt->fetch(PDO::FETCH_ASSOC);

                    if (!$item) {
                        continue; // Skip if product not found
                    }


                    $name = $item['Name'];
                    $price = (float) $item['Price'];
                    $total += $price * $qnn;

                    // Fallback image if not found
                    $imagePath = file_exists("img/menu-$id.jpeg") ? "img/menu-$id.jpeg" : "img/default.jpg";
            ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <img src="<?= $imagePath ?>" class="card-img-top" alt="<?= $name ?>" style="height:200px; object-fit:cover;">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= $name ?></h5>
                        <p class="card-text text-success font-weight-bold">Quantity: <?=$qnn ?></p>
                        <p class="card-text text-success font-weight-bold">Price Per One: $<?= number_format($price, 2) ?></p>
                        <a href="?delete=<?=$productId?>" class="btn btn-outline-danger btn-sm w-100 mt-2">🗑 Remove</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-4">
            <h4>Total: <span class="text-primary">$<?= number_format($total, 2) ?></span></h4>
            <a href="checkout.php" class="btn btn-success btn-lg mt-3">✅ Order Now</a>
        </div>
    <?php endif; ?>
</div>

<!-- Footer -->
<footer id="footer" class="bg-light text-dark py-3">
    <div class="container text-center">
        <p>&copy; All Rights Reserved - <a href="index.php">Italian Cuisine</a></p>
        <p>Designed by <a href="https://www.instagram.com/6_9oe?igsh=MXdza3VmcXUwNXphZg==">Mahmoud Mnajed</a></p>
    </div>
</footer>

<!-- Scripts -->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

</body>
</html>