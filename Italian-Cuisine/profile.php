<?php
    session_start();
    if (!isset($_SESSION['gmail'])) {
        echo '<div style="padding:40px;text-align:center;">
                <div style="color:red;font-size:22px;font-weight:bold;margin-bottom:20px;">
                    you are required to login first to see this page.
                </div>
                <a href="login.php" style="background:#c59d5f;color:#fff;padding:12px 32px;border-radius:25px;text-decoration:none;font-size:18px;">Go to Login</a>
            </div>';
        exit;
    }

    $gmail = $_SESSION['gmail'] ?? null;
    require 'functions.php';
    $conn = getPDOConnection();
    // var_dump($_SESSION);
    // echo "here11111111111111111111111111";
    try {
        $sql = "SELECT * from account where Gmail = :gmail ";
        // var_dump($sql);
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':gmail', $gmail);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($result) {
            foreach ($result as $client) {
                $firstName = $client['FirstName'];
                $lastName = $client['LastName'];
                $gmail = $client['Gmail'];
                $phoneNb = $client['PhoneNb'];
                $address = $client['Address'];
                $birthday = $client['Birthday'];
                $location = $client['Location'];
                //   $points = $client['Points'] ?? 0;
            }
            // print_r($result); // or use $client['Points'], etc.
        } else {
            echo "No matching client found.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    } finally {
        $stmt = null;
        $conn = null;
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
        <!-- Reservations Section Start -->
        <section id="reservations">
            <form action="./formReservation.php" method="post">
                <div class="container">
                    <header class="section-header">
                        <h3>Reservations</h3>
                    </header>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-row">
                                <div class="control-group col-sm-3">
                                    <input type="date" class="form-control" name="date" required />
                                </div>
                                <div class="control-group col-sm-3">
                                    <div class="form-group">
                                        <div class="input-group date" id="time" data-target-input="nearest">
                                            <input type="text" class="form-control datetimepicker-input" name="time"
                                                placeholder="Time" data-target="#time" required="required" />
                                            <div class="input-group-append" data-target="#time" data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-clock-o"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="control-group col-sm-3">
                                    <select class="custom-select" name="party_size" required>
                                        <option value="" selected disabled>Party Size</option>
                                        <option value="1">1 Person</option>
                                        <option value="2">2 People</option>
                                        <option value="3">3 People</option>
                                        <option value="4">4 People</option>
                                        <option value="5">5 People</option>
                                        <option value="6">6 People</option>
                                        <option value="7">7 People</option>
                                        <option value="8">8 People</option>
                                        <option value="9">9 People</option>
                                        <option value="10">10 People</option>
                                    </select>
                                </div>
                                <div class="control-group col-sm-3">
                                    <button type="submit" class="btn btn-block btn-book">Book Now</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </section>
        <!-- Reservations Section End -->

        <!-- Profile Section Start -->
        <section id="profile">
            <div class="container">
                <h2>Profile information</h2>
                <div class="profile-details">
                    <p><strong>Name:</strong> <?php echo "$firstName $lastName" ;?></p>
                    <p><strong>Email:</strong> <?php echo "$gmail" ;?></p>
                    <p><strong>Phone Number:</strong> <?php echo "$phoneNb" ;?></p>
                    <!-- echo account type -->
                    <p><strong>Account Type:</strong> <?php echo $_SESSION['accountType'] ?? 'Client'; ?></p>
                    <p><strong>Address:</strong> <?php echo "$address" ;?></p>
                    <p><strong>Location:</strong> <?php echo "$location" ;?></p>
                    <p><strong>Birthday:</strong> <?php echo "$birthday" ;?></p>
                    <!-- <p><strong>Points:</strong></p><?php echo "$Points" ;?>-->
                </div>
            </div>
        </section>
        <!-- Profile Section End -->

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
                                <a href="https://www.instagram.com/6_9oe?igsh=MXdza3VmcXUwNXphZg==">Mahmoud Mnajed</a>
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
