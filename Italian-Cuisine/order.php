<?php
session_start();
require "functions.php";

// Redirect if not a logged-in client
if (!isset($_SESSION['accountType']) || $_SESSION['accountType'] !== 'Client') {
    header("Location: login.php");
    exit;
}

$conn = getPDOConnection();
if (!$conn) {
    die("❌ Database connection failed.");
}

$clientId = $_SESSION['id'];

$stmt = $conn->prepare("SELECT * FROM `Order` WHERE ClientId = ? ORDER BY Date DESC");
$stmt->execute([$clientId]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>My Orders</title>
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
    <h2 class="mb-4">🧾 My Orders</h2>
    <?php if (count($orders) === 0): ?>
        <p>You have no orders yet.</p>
    <?php else: ?>
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Order ID</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Total ($)</th>
                    <th>Description</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= htmlspecialchars($order['Id']) ?></td>
                        <td><?= htmlspecialchars($order['Status']) ?></td>
                        <td><?= htmlspecialchars($order['Date']) ?></td>
                        <td><?= htmlspecialchars(number_format($order['Total'], 2)) ?></td>
                        <td><?= nl2br(htmlspecialchars($order['Description'])) ?></td>
                        <td><a href="orderid.php?id=<?= $order['Id'] ?>" class="btn btn-sm btn-primary">View</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</main>

<footer id="footer">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <div class="copyright">
                    <p>&copy; <?= date('Y') ?> <a href="index.php">Italian Cuisine</a>. All Rights Reserved</p>
                    <p>Designed By <a href="https://www.instagram.com/6_9oe">Mahmoud Mnajed</a></p>
                </div>
            </div>
            <div class="col-sm-6">
                <ul class="icon">
                    <li><a href="#" class="fa fa-twitter"></a></li>
                    <li><a href="#" class="fa fa-facebook"></a></li>
                    <li><a href="#" class="fa fa-pinterest"></a></li>
                    <li><a href="#" class="fa fa-google-plus"></a></li>
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
<script src="vendor/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>
