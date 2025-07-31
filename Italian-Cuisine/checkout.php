<?php
    session_start();
    require "functions.php";

    $conn = getPDOConnection();
    if (!$conn) {
        die("❌ Database connection failed.");
    }

    $stringToBeUsedLater = "";
    $total = 0.00; // Changed to float for monetary value
    $description = ""; // Initialize description variable
    $user = null;
    $success = false;
    if (isset($_SESSION['gmail'])) {
        $stmt = $conn->prepare("SELECT FirstName, LastName, PhoneNb, Address FROM account WHERE Gmail = ?");
        $stmt->execute([$_SESSION['gmail']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    if (!empty($_SESSION['cart'])) {
        try {
            // get client ID
            $sql = "SELECT AccountId FROM Client WHERE AccountId IN (
                    SELECT Id FROM Account WHERE Gmail = :email AND PhoneNb = :phoneNb
                )";
            $stmt = $conn->prepare($sql);
            if (!isset($_SESSION['gmail'])) {
    die("You must be logged in to checkout. <a href='login.php'>Login here</a>");
}
            $stmt->execute([
                'email' => $_SESSION['gmail'],
                'phoneNb' => $user['PhoneNb'] ?? '',
            ]);
            $client = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$client) {
                die("Client not found.");
            }
            $clientId = $client['AccountId'];

                // find the total price of the products in the cart
                // write the string to be used later
            foreach ($_SESSION['cart'] as $productId => $qnn) {
            $qnn = (int) $qnn;
            $stmt = $conn->prepare("SELECT Name, Price FROM product WHERE Id = ?");
            $stmt->execute([$productId]);
            $item = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($item) {
                    $price = (float) $item['Price'];
                    $item['name'] = htmlspecialchars($item['Name']);    
                    $total += $price * $qnn;
                    // make the description contains the product name, id and quantity
                    $description .= "Item id: " . $productId . ", Name: " . $item['name'] . ", Quantity: " . $qnn . " | ";
                    $stringToBeUsedLater .= '<li class="list-group-item d-flex justify-content-between">';
                    $stringToBeUsedLater .= htmlspecialchars($item['name']);
                    $stringToBeUsedLater .= '<span>$' . number_format($price, 2) . '</span>';
                    $stringToBeUsedLater .= '</li>';
                } else {
                    echo "Product with ID $productId not found.<br>";
                }
            }

            $_SESSION['description'] = $description; // Store description in session
            $_SESSION['total'] = $total; // Store total in session for later use   
            $success = true;

        } catch (PDOException $e) {
            die ("❌ Failed to place order: " . $e->getMessage());
        }
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
    <meta charset="UTF-8">
    <title>Checkout</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
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

    <div class="container my-5">
        <h2 class="text-center mb-4">🧾 Checkout</h2>

        <?php if (!$success): ?>
            <div class="alert alert-danger text-center"><?= "Please fill in all fields and ensure the cart is not empty." ?></div>
        <?php else: ?>
        <form action="checkoutProcess.php" method="post" class="mb-4">
            <?php
                $default_name = $user ? $user['FirstName'] . ' ' . $user['LastName'] : '';
                $default_phone = $user['PhoneNb'] ?? '';
                $default_address = $user['Address'] ?? '';
            ?>

            <div class="mb-3">
                <label for="name">Full Name:</label>
                <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($default_name) ?>">
            </div>

            <div class="mb-3">
                <label for="name">Description:</label>
                <input type="text"  readonly name="description" class="form-control" required value="<?= htmlspecialchars($description) ?>">
            </div>

            <div class="mb-3">
                <label for="phone">Phone Number:</label>
                <input type="text" name="phone" class="form-control" required value="<?= htmlspecialchars($default_phone) ?>">
            </div>

            <div class="mb-3">
                <label for="address">Delivery Address:</label>
                <textarea name="address" class="form-control" rows="3" required><?= htmlspecialchars($default_address) ?></textarea>
            </div>

            <h4>🛍 Order Summary:</h4>
            <ul class="list-group mb-3">
                <?php echo $stringToBeUsedLater;?>
                <li class="list-group-item d-flex justify-content-between font-weight-bold">
                    Total
                    <span>$<?= number_format($total, 2) ?></span>
                </li>
            </ul>
            <button type="submit" class="btn btn-success btn-block">✅ Place Order</button>
        </form>
        <?php endif; ?>
    </div>

    <!-- Footer Start -->
        <footer id="footer">
            <div class="container">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="copyright">
							<p>&copy; Copyright <a href="">Italian Cusine</a>. All Rights Reserved</p>
							
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

</body>
</html>