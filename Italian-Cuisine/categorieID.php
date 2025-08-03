<?php
    session_start();
    require "functions.php";

    $categoryId = $_GET['id'] ?? null;

    if (!$categoryId) {
        header("Location: menu.php");
        exit;
    }

    $conn = getPDOConnection();
    if (!$conn) {
        die("❌ Database connection failed.");
    }

    try {
        // Get category name
        $stmt = $conn->prepare("SELECT Name FROM Categorie WHERE Id = ?");
        $stmt->execute([$categoryId]);
        $category = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$category) {
            echo "❌ Category not found.";
            exit;
        }

        // Get products
        $stmt = $conn->prepare("SELECT * FROM Product WHERE CategorieId = ?");
        $stmt->execute([$categoryId]);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die( "❌ Database error: " . $e->getMessage());
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title><?= htmlspecialchars($category['Name']) ?> - Products</title>
        <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>
        <!-- Top Header (identique) -->
        <section id="top-header">
            <div class="logo">
                <img src="img/logo.png" />
            </div>
        </section>

        <!-- Header (identique) -->
        <header id="header">
            <div class="container">
                <nav id="nav-menu-container">
                    <ul class="nav-menu">
                        <li><a href="profile.php">👥</a></li>
                        <?php if (!isset($_SESSION['gmail'])): ?>
                            <li><a href="login.php">Login</a></li>
                        <?php endif; ?>
                        <li><a href="index.php">Home</a></li>
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

        
        <div class="container my-5">
            <h2 class="mb-4"><?= htmlspecialchars($category['Name']) ?> Products</h2>
            <div class="row">
                <?php if (empty($products)): ?>
                    <p>No products found in this category.</p>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card">
                                <img src="img/menu-<?= htmlspecialchars($product['Id']) ?>.jpeg" class="card-img-top" alt="<?= htmlspecialchars($product['Name']) ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($product['Name']) ?></h5>
                                    <p class="card-text"><?= htmlspecialchars($product['Description']) ?></p>
                                    <p class="text-primary"><?= number_format($product['Price'], 2) ?> $</p>
                                    <form method="POST" action="cart.php">
                                        <input type="hidden" name="product_id" value="<?= $product['Id'] ?>">
                                        <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['Name']) ?>">
                                        <input type="hidden" name="product_price" value="<?= $product['Price'] ?>">
                                        <button type="submit" class="btn btn-success">Add to Cart</button>
                                    </form>
                                    <a href="productId.php?id=<?= $product['Id'] ?>" class="btn btn-secondary mt-2">View Details</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <a href="menu.php" class="btn btn-link mt-4">← Back to Menu</a>
        </div>


        <!-- Footer Start -->
        <footer id="footer">
            <div class="container">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="copyright">
							<p>&copy; Copyright <a href="index.php">Italian Cusine</a>. All Rights Reserved</p>
							
							<!--/*** This template is free as long as you keep the footer author’s credit link/attribution link/backlink. If you'd like to use the template without the footer author’s credit link/attribution link/backlink, you can purchase the Credit Removal License from "https://htmlcodex.com/credit-removal". Thank you for your support. ***/-->
							<p>Designed By  
                                <a href="https://www.linkedin.com/in/waed-mansour-19ba52324/">Waed Mansour</a> &amp;
                                <a href="https://www.instagram.com/6_9oe?igsh=MXdza3VmcXUwNXphZg==">Mahmoud Mnajed</a></p>
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