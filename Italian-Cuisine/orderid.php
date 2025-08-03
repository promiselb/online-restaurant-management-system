<?php
session_start();
require "functions.php";

if (!isset($_SESSION['accountType']) || $_SESSION['accountType'] !== 'Client') {
    header("Location: login.php");
    exit;
}

$orderId = $_GET['id'] ?? null;
if (!$orderId || !is_numeric($orderId)) {
    echo "❌ Invalid order ID.";
    exit;
}

$conn = getPDOConnection();
if (!$conn) {
    die("❌ Database connection failed.");
}

$clientId = $_SESSION['id'];

// Fetch the order info, make sure it belongs to the client
$stmt = $conn->prepare("SELECT * FROM `Order` WHERE Id = ? AND ClientId = ?");
$stmt->execute([$orderId, $clientId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo "❌ Order not found or access denied.";
    exit;
}

// Fetch products in the order
$stmt = $conn->prepare("SELECT p.Name, p.Price, op.Quantity FROM OrderProduct op JOIN Product p ON op.ProductId = p.Id WHERE op.OrderId = ?");
$stmt->execute([$orderId]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch delivery man name if available
$deliveryManInfo = null;
$FullName = '';
if (!empty($order['DeliveryManId'])) {
    $stmt = $conn->prepare("SELECT FirstName, LastName FROM Deliveryman WHERE AccountId = ?");
    $stmt->execute([$order['DeliveryManId']]);
    $deliveryManInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    $FullName = $deliveryManInfo ? $deliveryManInfo['FirstName'] . ' ' . $deliveryManInfo['LastName'] : '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Order #<?= htmlspecialchars($orderId) ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
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

<main class="container my-5">
    <h2 class="mb-4">Order #<?= htmlspecialchars($orderId) ?></h2>

    <div class="mb-3">
        <strong>Status:</strong> <?= htmlspecialchars($order['Status']) ?><br>
        <strong>Date:</strong> <?= htmlspecialchars($order['Date']) ?><br>
        <strong>Total:</strong> $<?= htmlspecialchars(number_format($order['Total'], 2)) ?><br>
        <strong>Description:</strong><br>
        <p><?= nl2br(htmlspecialchars($order['Description'])) ?></p>
        <strong>Delivery Man ID:</strong> <?= htmlspecialchars($order['DeliveryManId'] ?? 'N/A') ?><br>
        <strong>Delivery Man Name:</strong> <?= $deliveryManInfo ? $FullName : 'N/A' ?><br>
    </div>

    <h4>Products</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Unit Price ($)</th>
                <th>Quantity</th>
                <th>Subtotal ($)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?= htmlspecialchars($product['Name']) ?></td>
                    <td><?= number_format($product['Price'], 2) ?></td>
                    <td><?= (int)$product['Quantity'] ?></td>
                    <td><?= number_format($product['Price'] * $product['Quantity'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a href="order.php" class="btn btn-secondary">⬅ Back to Orders</a>
</main>

<footer id="footer">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <div class="copyright">
                    <p>&copy; <?= date('Y') ?> <a href="index.php">Italian Cuisine</a>. All Rights Reserved</p>
                    <p>Designed By 
                        <a href="https://www.linkedin.com/in/waed-mansour-19ba52324/">Waed Mansour</a> &amp;
                        <a href="https://www.instagram.com/6_9oe?igsh=MXdza3VmcXUwNXphZg==">Mahmoud Mnajed</a>
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

<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>
