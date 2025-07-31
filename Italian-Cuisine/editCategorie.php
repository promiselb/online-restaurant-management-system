<?php
session_start();
require "functions.php";

// Admin-only access
if (!isset($_SESSION['accountType']) || $_SESSION['accountType'] !== 'Admin') {
    echo "❌ Access denied.";
    exit;
}

$conn = getPDOConnection();
if (!$conn) {
    die("❌ Database connection failed.");
}

$categorieId = $_GET['id'] ?? null;
if (!$categorieId || !is_numeric($categorieId)) {
    echo "❌ Invalid category ID.";
    exit;
}

// Fetch category
$stmt = $conn->prepare("SELECT * FROM Categorie WHERE Id = ?");
$stmt->execute([$categorieId]);
$categorie = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$categorie) {
    echo "❌ Category not found.";
    exit;
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');

    if ($name) {
        $update = $conn->prepare("UPDATE Categorie SET Name = ? WHERE Id = ?");
        $success = $update->execute([$name, $categorieId]);

        if ($success) {
            $message = "<p style='color:green;'>✅ Category updated successfully.</p>";
            $stmt->execute([$categorieId]);
            $categorie = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $message = "<p style='color:red;'>❌ Failed to update category.</p>";
        }
    } else {
        $message = "<p style='color:red;'>❌ Name cannot be empty.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Edit Category</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <link href="vendor/animate/animate.min.css" rel="stylesheet">
    <link href="vendor/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="vendor/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />
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

    <main class="container my-5">
        <h2>Edit Category (ID: <?= htmlspecialchars($categorie['Id']) ?>)</h2>
        <?= $message ?? '' ?>

        <form method="post" class="mt-4">
            <div class="mb-3">
                <label for="name" class="form-label">Category Name</label>
                <input id="name" class="form-control" type="text" name="name"
                       value="<?= htmlspecialchars($categorie['Name']) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Category</button>
        </form>

        <p class="mt-4"><a href="manage.php">← Back to Manage Page</a></p>
    </main>

    <footer id="footer">
        <div class="container">
            <div class="row">
                <div class="col-sm-6">
                    <div class="copyright">
                        <p>&copy; <?= date('Y') ?> <a href="index.php">Italian Cuisine</a>. All Rights Reserved</p>
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

    <a href="#" class="back-to-top"><i class="fa fa-chevron-up"></i></a>
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
    <script src="js/main.js"></script>
</body>
</html>
