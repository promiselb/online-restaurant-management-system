<?php
    session_start();
    require "functions.php";

    // Check if user is admin
    if (!isset($_SESSION['accountType']) || $_SESSION['accountType'] !== 'Admin') {
        // header("Location: login.php");
        echo "❌ You do not have permission to access this page.";
        echo $_SESSION['accountType'];
        exit;
    }

    
    $conn = getPDOConnection();
    if (!$conn) {
        die("❌ Database connection failed.");
    }

    // Handle order status updates
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['update_status'])) {
            $orderId = (int)$_POST['order_id'];
            $newStatus = $_POST['new_status'];
            
            try {
                $stmt = $conn->prepare("UPDATE `Order` SET Status = ? WHERE Id = ?");
                $stmt->execute([$newStatus, $orderId]);
                $success = "Order status updated successfully!";
            } catch (PDOException $e) {
                $error = "Error updating order: " . $e->getMessage();
            }
        }
        
        if (isset($_POST['delete_order'])) {
            $orderId = (int)$_POST['order_id'];
            
            try {
                // First delete from OrderProduct (due to foreign key constraint)
                $stmt = $conn->prepare("DELETE FROM OrderProduct WHERE OrderId = ?");
                $stmt->execute([$orderId]);
                
                // Then delete from Order
                $stmt = $conn->prepare("DELETE FROM `Order` WHERE Id = ?");
                $stmt->execute([$orderId]);
                
                $success = "Order deleted successfully!";
            } catch (PDOException $e) {
                $error = "Error deleting order: " . $e->getMessage();
            }
        }
    }

    // Fetch all non-delivered orders with client and product details
    try {
        $stmt = $conn->prepare("
            SELECT o.Id, o.Status, o.Total, o.Date, 
                a.FirstName, a.LastName, a.PhoneNb, a.Address,
                GROUP_CONCAT(CONCAT(p.Name, ' (x', op.Quantity, ')') SEPARATOR ', ') AS Products
            FROM `Order` o
            JOIN Client c ON o.ClientId = c.AccountId
            JOIN Account a ON c.AccountId = a.Id
            JOIN OrderProduct op ON o.Id = op.OrderId
            JOIN Product p ON op.ProductId = p.Id
            WHERE o.Status != 'Delivered'
            GROUP BY o.Id
            ORDER BY o.Date DESC
        ");
        $stmt->execute();
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error fetching orders: " . $e->getMessage());
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Admin - Order Management</title>
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
    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .status-pending { background-color: #fff3cd; }
        .status-processing { background-color: #cce5ff; }
        .status-shipped { background-color: #d4edda; }
        .status-cancelled { background-color: #f8d7da; }
    </style>
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

        <main>
            <div class="container-fluid mt-4">
                <h2 class="mb-4">📋 Order Management</h2>
                
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php elseif (!empty($success)): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>Order ID</th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Contact</th>
                                <th>Products</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                            <tr class="status-<?= strtolower($order['Status']) ?>">
                                <td><?= htmlspecialchars($order['Id']) ?></td>
                                <td><?= htmlspecialchars($order['Date']) ?></td>
                                <td><?= htmlspecialchars($order['FirstName'] . ' ' . $order['LastName']) ?></td>
                                <td>
                                    <?= htmlspecialchars($order['PhoneNb']) ?><br>
                                    <?= htmlspecialchars($order['Address']) ?>
                                </td>
                                <td><?= htmlspecialchars($order['Products']) ?></td>
                                <td>$<?= number_format($order['Total'], 2) ?></td>
                                <td>
                                    <form method="post" class="form-inline">
                                        <input type="hidden" name="order_id" value="<?= $order['Id'] ?>">
                                        <select name="new_status" class="form-control form-control-sm">
                                            <option value="Pending" <?= $order['Status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="Processing" <?= $order['Status'] === 'Processing' ? 'selected' : '' ?>>Processing</option>
                                            <option value="Shipped" <?= $order['Status'] === 'Shipped' ? 'selected' : '' ?>>Shipped</option>
                                            <option value="Delivered" <?= $order['Status'] === 'Delivered' ? 'selected' : '' ?>>Delivered</option>
                                            <option value="Cancelled" <?= $order['Status'] === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                        </select>
                                        <button type="submit" name="update_status" class="btn btn-sm btn-primary ml-2">Update</button>
                                    </form>
                                </td>
                                <td>
                                    <form method="post" onsubmit="return confirm('Are you sure you want to delete this order?');">
                                        <input type="hidden" name="order_id" value="<?= $order['Id'] ?>">
                                        <button type="submit" name="delete_order" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            
                            <?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="8" class="text-center">No pending orders found</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="container mt-5">
                <h2 class="mb-4">Admin Keys</h3>
                <div class="row">
                    <div class="col-md-6">
                        <h4>Existing Keys</h4>
                            <?php
                                $ask_array = getAdminSecretKeys();
                                if (empty($ask_array)) {
                                    echo "<li class='list-group-item'>No keys found</li>";
                                } else {
                                    $keys = getAdminSecretKeys(); // This should return an array of secret key strings

                                    $table = "<table border='1' cellpadding='8' cellspacing='0'>";
                                    $table .= "<tr><th>Secret Key</th><th>Action</th></tr>";

                                    foreach ($keys as $key) {
                                        $encodedKey = urlencode($key); // for safe URL
                                        $table .= "<tr>";
                                        $table .= "<td>" . htmlspecialchars($key) . "</td>";
                                        $table .= "<td><a href='deleteAdminKey.php?key={$encodedKey}' onclick=\"return confirm('Are you sure you want to delete this key?');\">Delete</a></td>";
                                        $table .= "</tr>";
                                    }

                                    $table .= "</table>";

                                    // Output the table
                                    echo $table;
                                }
                            ?>
                        <h4 class="mt-4">Generate New Key</h4>
                        <button class="btn btn-primary" onclick="location.href='generateAdminKey.php'">Generate Key</button>
                    </div>
                <div class="row">
                    <h2 class="mb-4">Products & Categories</h2>
                    <div class="col-md-6">
                        <button class="btn btn-primary" onclick="location.href='manage.php'">Manage Products</button>
                    </div>
                    
                </div>    
            </div>
        </main>

        <!-- Footer Start -->
        <footer id="footer">
            <div class="container">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="copyright">
							<p>&copy; Copyright <a href="">Italian Cusine</a>. All Rights Reserved</p>
							
							<!--/*** This template is free as long as you keep the footer author’s credit link/attribution link/backlink. If you'd like to use the template without the footer author’s credit link/attribution link/backlink, you can purchase the Credit Removal License from "https://htmlcodex.com/credit-removal". Thank you for your support. ***/-->
                            <p>Designed By 
                                <a href="https://www.linkedin.com/in/waed-mansour-19ba52324/">Waed Mansour</a> &amp;
                                <a href="https://www.instagram.com/6_9oe?igsh=MXdza3VmcXUwNXphZg==">Mahmoud Mnajed</a>
                            </p>
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

<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>