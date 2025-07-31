<?php
session_start();
require "functions.php";

// Get the product ID from the URL
$productId = $_GET['id'] ?? null;

if (!$productId) {
    header("Location: menu.php");
    exit;
}

// Connect to DB
$conn = getPDOConnection();

// Get product details
try {
    $stmt = $conn->prepare("SELECT * FROM Product WHERE Id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo "❌ Product not found.";
        exit;
    }

} catch (PDOException $e) {
    die("❌ Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($product['Name']) ?> - Product Details</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
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

<!-- Product Details -->
<div class="container my-5">
    <div class="row">
        <div class="col-md-6">
            <img src="img/menu-<?= htmlspecialchars($product['Id']) ?>.jpeg" class="img-fluid" alt="<?= htmlspecialchars($product['Name']) ?>">
        </div>
        <div class="col-md-6">
            <h2><?= htmlspecialchars($product['Name']) ?></h2>
            <p class="text-muted"><?= number_format($product['Price'], 2) ?> $</p>
            <p><?= nl2br(htmlspecialchars($product['Description'])) ?></p>

            <form action="cart.php" method="POST">
                <input type="hidden" name="product_id" value="<?= $product['Id'] ?>">
                <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['Name']) ?>">
                <input type="hidden" name="product_price" value="<?= $product['Price'] ?>">
                <button type="submit" class="btn btn-success">Add to Cart</button>
            </form>

            <a href="menu.php" class="btn btn-secondary mt-3">← Back to Menu</a>
        </div>
    </div>
</div>

 <!-- Footer Start -->
        <footer id="footer">
            <div class="container">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="copyright">
							<p>&copy; Copyright <a href="index.php">Italian Cusine</a>. All Rights Reserved</p>
							
							<!--/*** This template is free as long as you keep the footer author’s credit link/attribution link/backlink. If you'd like to use the template without the footer author’s credit link/attribution link/backlink, you can purchase the Credit Removal License from "https://htmlcodex.com/credit-removal". Thank you for your support. ***/-->
							<p>Designed By <a href="https://www.instagram.com/6_9oe?igsh=MXdza3VmcXUwNXphZg==">Mahmoud Mnajed</a></p>
						</div>
                    </div>
                    <div class="col-sm-6">
                        <ul class="icon">
                            <li><a href="index.php" class="fa fa-twitter"></a></li>
                            <li><a href="index.php" class="fa fa-facebook"></a></li>
                            <li><a href="index.php" class="fa fa-pinterest"></a></li>
                            <li><a href="index.php" class="fa fa-google-plus"></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </footer>
        <!-- Footer end -->

        <a href="#" class="back-to-top"><i class="fa fa-chevron-up"></i></a>

        <!-- JavaScript Libraries -->
        <script src="vendor/jquery/jquery.min.js"></script>
        <script src="vendor/jquery/jquery-migrate.min.js"></script>
        <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="vendor/easing/easing.min.js"></script>
        <script src="vendor/stickyjs/sticky.js"></script>
        <script src="vendor/superfish/hoverIntent.js"></script>
        <script src="vendor/superfish/superfish.min.js"></script>
        <script src="vendor/owlcarousel/owl.carousel.min.js"></script>
        <script src="vendor/tempusdominus/js/moment.min.js"></script>
        <script src="vendor/tempusdominus/js/moment-timezone.min.js"></script>
        <script src="vendor/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

        <!-- Main Javascript File -->
        <script src="js/main.js"></script>

    </body>
</html>