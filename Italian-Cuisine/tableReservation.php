<?php
require "functions.php";
session_start();

$pdo = getPDOConnection();

$success_message = '';
$error_message = '';
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
                        <li><a href="profile.php">👥</a></li>
                        <?php if (!isset($_SESSION['gmail'])): ?>
                          <li><a href="login.php">Login</a></li>
                        <?php endif; ?>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="about.php">About</a></li>
                        <li><a href="menu.php">Menu</a></li>
                        <!--<li><a href="order.php">Online Order</a></li> -->                                
                        <li><a href="contact.php">Contact</a></li>
                        <li><a href="cart.php">🛒</a></li>
                        <?php if (isset($_SESSION['gmail'])): ?>
                        <li><a href="logout.php">Logout</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </header>
          <!-- Header End -->

           <?php
        // معالجة تحديث الحجز
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // تحديث الحجز
            if (isset($_POST['edit_reservation'])) {
                $reservation_id = (int)$_POST['reservation_id'];
        $date = $_POST['date'] ?? '';
        $time = $_POST['time'] ?? '';
        $party_size = isset($_POST['party_size']) ? (int)$_POST['party_size'] : 0;

        $time_24 = $time ? date("H:i:s", strtotime($time)) : '';
        $is_future = false;
        if ($date && $time_24) {
            $reservation_datetime = strtotime("$date $time_24");
            if ($reservation_datetime > time()) {
                $is_future = true;
            }
        }

        if ($date && $time_24 && $party_size > 0 && $is_future) {
            $stmt = $pdo->prepare("UPDATE reservations SET date = ?, time = ?, party_size = ? WHERE id = ?");
            if ($stmt->execute([$date, $time_24, $party_size, $reservation_id])) {
                $success_message = "✅ the update reservation has been successfully.";
            } else {
                $error_message = "❌ An error occurred while updating the reservation.";
            }
        } else {
            $error_message = "❌ Please ensure all fields are filled out correctly and the date and time are in the future.";
        }
    }

    // حذف الحجز
    if (isset($_POST['delete_reservation'])) {
        $reservation_id = (int)$_POST['reservation_id'];

        $stmt = $pdo->prepare("DELETE FROM reservations WHERE id = ?");
        if ($stmt->execute([$reservation_id])) {
            $success_message = "✅ The reservation has been successfully canceled.";
        } else {
            $error_message = "❌ An error occurred while canceling the reservation.";
        }
    }
}

// جلب كل الحجوزات مع اسم العميل
$stmt = $pdo->prepare("
    SELECT r.id, r.date, r.time, r.party_size, a.FirstName, a.LastName
    FROM reservations r
    JOIN client c ON r.client_id = c.AccountId
    JOIN account a ON c.AccountId = a.Id
    ORDER BY r.date DESC, r.time DESC
");
$stmt->execute();
$reservations = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Reservation Management</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; direction: rtl; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background-color: #f0f0f0; }
        form.inline { margin: 0; display: inline-block; }
        input, select { padding: 5px; width: 100%; box-sizing: border-box; }
        button { padding: 5px 10px; cursor: pointer; }
        button.delete { background-color: #d9534f; color: white; border: none; }
        .message { font-weight: bold; margin-bottom: 15px; }
        .success { color: green; }
        .error { color: red; }
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

<h2>Reservation Management</h2>

<?php if ($success_message): ?>
    <div class="message success"><?= htmlspecialchars($success_message) ?></div>
<?php endif; ?>

<?php if ($error_message): ?>
    <div class="message error"><?= htmlspecialchars($error_message) ?></div>
<?php endif; ?>

<table>
    <thead>
        <tr>
            <th>table_id</th>
            <th>clientName</th>
            <th>Date</th>
            <th>Time</th>
            <th>Party Size</th>
            <th>Update</th>
            <th>Cancel</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($reservations as $res): ?>
            <tr>
                <td><?= $res['id'] ?></td>
                <td><?= htmlspecialchars($res['FirstName'] . ' ' . $res['LastName']) ?></td>
                <td>
                    <form method="POST" class="inline">
                        <input type="hidden" name="reservation_id" value="<?= $res['id'] ?>">
                        <input type="date" name="date" value="<?= $res['date'] ?>" required>
                </td>
                <td>
                        <input type="time" name="time" value="<?= $res['time'] ?>" required>
                </td>
                <td>
                        <select name="party_size" required>
                            <?php for ($i = 1; $i <= 10; $i++): ?>
                                <option value="<?= $i ?>" <?= ($res['party_size'] == $i) ? 'selected' : '' ?>><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                </td>
                <td>
                        <button type="submit" name="edit_reservation">Update</button>
                    </form>
                </td>
                <td>
                    <!-- نموذج الحذف -->
                    <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to cancel this reservation?');">
                        <input type="hidden" name="reservation_id" value="<?= $res['id'] ?>">
                        <button type="submit" name="delete_reservation" class="delete">Cancel</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
<html>
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