<?php
session_start();
if (isset($_SESSION['gmail'])) {
    header("Location: index.php");
    exit;
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

        <!-- Login Section Start -->
        <section id="login">
            <div class="container">
                <div class="section-header">
                    <h3>Sign Up / Sign In</h3>
                    <h3>As Admin</h3>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="login-col-1">
                            <div class="login-form">
                                <form action="./formAdminSignUp.php" method="post">
                                    <!-- First & Last Names -->
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <input type="text" class="form-control" placeholder="First Name" name="firstName" required="required" />
                                        </div>
                                        <div class="form-group col-md-6">
                                            <input type="text" class="form-control" placeholder="Last Name" name="lastName" required="required" />
                                        </div>
                                    </div>

                                    <!-- Phone Number & Email -->
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <input type="text" class="form-control" placeholder="Your Phone Number" pattern="\d{2}\d{3}\d{3}" name="phoneNb" required="required" />
                                        </div>
                                        <div class="form-group col-md-6">
                                            <input type="email" class="form-control" placeholder="Your Email" name="email" required="required" />
                                        </div>
                                    </div>

                                    <!-- Address & Location -->
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <textarea class="form-control" placeholder="Your Address" name="address" required="required"></textarea>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <input type="url" class="form-control" placeholder="Your Location" name="location" required="required" />
                                        </div>
                                    </div>

                                    <!-- Age & Birthday -->
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <input title="At least is today and not older than 1900" type="date" min="1900-01-01" max="<?php echo date('Y-m-d'); ?>" class="form-control" placeholder="Your Birthday" name="birthday" required="required" />
                                        </div>
                                    </div>

                                    <!-- Password1 & Password2 -->
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <input type="password" class="form-control" placeholder="Your Password" name="password1" required="required" />
                                        </div>
                                        <div class="form-group col-md-6">
                                            <input type="password" class="form-control" placeholder="Repeat Your Password" name="password2" required="required" />
                                        </div>
                                    </div>

                                     <!-- Admin Secret Key (ask) -->
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <input title="Admin Secret Key" type="text" class="form-control" placeholder="Admin Secret Key" name="ask" required="required" /> 
                                        </div>
                                    </div>
                                    <div><button type="submit">sign up</button></div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="login-col-2">
                            <div class="login-form">
                                <form action="./formAdminSignIn.php" method="post">
                                    <div class="form-group">
                                        <input type="email" class="form-control" placeholder="Your Email" name="email" required="required" />
                                    </div>
                                    <div class="form-group">
                                        <input type="password" class="form-control" placeholder="Your Password" name="password" required="required" />
                                    </div>
                                    <div><button type="submit">sign in</button></div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Login Section End -->

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