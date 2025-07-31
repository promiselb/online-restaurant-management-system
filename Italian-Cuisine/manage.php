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

    // Fetch all products
    $stmt = $conn->query("SELECT * FROM Product"); 
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!$products) {
        $products = [];
    }
    // Fetch all categories
    $stmt = $conn->query("SELECT * FROM Categorie");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!$categories) {
        $categories = [];
    }       


?>

<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="utf-8">
        <title>Italian Cuisine</title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport">
        <meta content="" name="keywords">
        <meta content="" name="description">

        <!-- Favicons -->
        <link href="img/favicon.ico" rel="icon">
        <link href="img/apple-touch-icon.png" rel="apple-touch-icon">

        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Montserrat:200,300,400,500,600|Pacifico" rel="stylesheet">

        <!-- Bootstrap CSS File -->
        <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

        <!-- Libraries CSS Files -->
        <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet">
        <link href="vendor/animate/animate.min.css" rel="stylesheet">
        <link href="vendor/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
        <link href="vendor/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

        <!-- Main Stylesheet File -->
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

        <!-- Main -->
        <main>

            <!-- Categories Section Start -->
            <div class="container my-5">
                <?php if (count($categories) > 0): ?>
                    <h2 class="mb-4">Categories</h2>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Edit</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td><?= htmlspecialchars($category['Id']) ?></td>
                                    <td><?= htmlspecialchars($category['Name']) ?></td>
                                    <!-- a link to edit it and a link to delete it -->
                                    <td><a href="editCategorie.php?id=<?= htmlspecialchars($category['Id']) ?>" class="btn btn-primary">Edit</a></td>
                                    <td><a href="deleteCategorie.php?id=<?= htmlspecialchars($category['Id']) ?>" class="btn btn-danger">Delete</a></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <!-- Form to add new category -->
                    <div class="container mt-5">
                        <form action="addCategorie.php" method="post" class="mt-4">
                            <div class="mb-3">
                                <label for="name" class="form-label">Category Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Add Category</button>
                        </form>
                    </div>
                <?php else: ?>
                    <p>No categories found.</p>
                <?php endif; ?>
            </div>
            <!-- Categories Section End -->

            <!-- Products Section -->
            <div class="container my-5">
                <h2 class="mb-4">Products</h2>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Category ID</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?= htmlspecialchars($product['Id']) ?></td>
                                <td><?= htmlspecialchars($product['Name']) ?></td>
                                <td><?= htmlspecialchars($product['Description']) ?></td>
                                <td><?= number_format($product['Price'], 2) ?> $</td>
                                <td><?= htmlspecialchars($product['CategorieId']) ?></td>
                                <!-- a link to edit it and a link to delete it -->
                                <td><a href="editProduct.php?id=<?= htmlspecialchars($product['Id']) ?>" class="btn btn-primary">Edit</a></td>
                                <td><a href="deleteProduct.php?id=<?= htmlspecialchars($product['Id']) ?>" class="btn btn-danger">Delete</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <!-- Form to add new product -->
                <form action="addProduct.php" method="POST" class="mt-4">
                    <div class="mb-3">
                        <label for="name" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Price</label>
                        <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                    </div>
                    <div class="mb-3">
                        <label for="categorieId" class="form-label">Category</label>
                        <select class="form-select" id="categorieId" name="categorieId" required>
                            <option value="">Select a category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= htmlspecialchars($category['Id']) ?>">
                                    <?= htmlspecialchars($category['Name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Product</button>
                </form>
            </div>
            <!-- Products Section End -->
        </main>
        

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