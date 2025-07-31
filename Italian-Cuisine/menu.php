<?php
session_start();
require "functions.php";

$conn = getPDOConnection();
if (!$conn) {
    die("❌ Database connection failed.");
}

// جلب الكاتيجوريز
try {
    $stmtCat = $conn->query("SELECT * FROM Categorie");
    $categories = $stmtCat->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("❌ Error fetching categories: " . $e->getMessage());
}

// جلب المنتجات النشطة
$stmtProd = $conn->prepare("SELECT * FROM product");
$stmtProd->execute();
$products = $stmtProd->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Menu</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- CSS & Fonts -->
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

<!-- Categories Section -->
<div class="container my-5">
    <h2 class="text-center mb-4">📂 Categories</h2>
    <div class="row">
        <?php foreach ($categories as $category): ?>
            <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
                <div class="single-category p-3 text-center" style="background:#fff;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,0.07);height:100%;">
                    <h4 class="mb-3" style="font-weight:600; color:#222;"><?= htmlspecialchars($category['Name']) ?></h4>
                    <a href="categorieID.php?id=<?= htmlspecialchars($category['Id']) ?>" class="btn btn-primary btn-block" style="border-radius:10px;">View Products</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Menu Section -->
<div class="container my-5">
    <h2 class="text-center mb-4">🍽 Our Delicious Menu</h2>
    <div class="row">

    <?php foreach ($products as $product): ?>
        <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
            <div class="single-menu p-3 d-flex flex-column justify-content-between text-center" style="background:#fff;border-radius:15px;box-shadow:0 4px 8px rgba(0,0,0,0.05);height:100%;">
                <img class="img-fluid mb-3" src="img/menu-<?= $product['Id'] ?>.jpeg" alt="<?= htmlspecialchars($product['Name']) ?>" style="border-radius:12px;max-height:180px;object-fit:cover;width:100%;">
                <h4 class="mb-2" style="font-size:1.1rem;font-weight:600;color:#222;"><?= htmlspecialchars($product['Name']) ?></h4>
                <div class="mb-2" style="color:#28a745;font-weight:600;">$<?= htmlspecialchars($product['Price']) ?></div>
                <div class="mb-3" style="font-size:0.95rem;color:#666;"><?= htmlspecialchars($product['Description']) ?></div>
                <div style="font-size:0.95rem;"><a class="btn btn-secondary mt-2" href="productid.php?id=<?=$product['Id']?>">View Details</a></div>
                <form method="post" action="cart.php" class="mt-auto">
                    <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['Id']) ?>">
                    <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['Name']) ?>">
                    <input type="hidden" name="product_price" value="<?= htmlspecialchars($product['Price']) ?>">
                    <button type="submit" class="btn btn-primary btn-block" style="border-radius:10px;">🛒 Add to Cart</button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>

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