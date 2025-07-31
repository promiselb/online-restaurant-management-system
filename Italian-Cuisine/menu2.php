<?php
    session_start();

    require "functions.php";   
    $conn = getPDOConnection();
    if (!$conn) {
        die("❌ Connection failed.");
    }
    

    // to display the categories in the menu
    // Prepare the SQL statement to fetch all categories
    $displayCategories = "";
    try {
        $stmt = $conn->query("SELECT * FROM Categorie");
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("❌ Error fetching categories: " . $e->getMessage());
    }

    // Check if there are categories or not
    if (empty($categories)) {
        echo '<div style="text-align:center;padding:40px;font-size:22px;color:red;">❌ No categories found.</div>';
    }
    
    // Parse the categories and prepare the display
    foreach ($categories as $category) {
        $displayCategories .= '<div class="col-sm-6 col-md-4 col-lg-3 mb-4">';
        $displayCategories .= '  <div class="single-category p-3" style="background:#fff;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,0.07);height:100%;">';
        $displayCategories .= '    <h4 class="mt-2 mb-1" style="font-size:1.1rem;font-weight:600;color:#222;">' . htmlspecialchars($category['Name']) . '</h4>';
        $displayCategories .= '    <a href="categorieID.php?id=' . htmlspecialchars($category['Id']) . '" class="btn btn-primary btn-block">View Products</a>';
        $displayCategories .= '  </div>';
        $displayCategories .= '</div>';
    }


    // To display the products in the menu
    // Prepare the SQL statement to fetch all products
    try {
            $sql = "SELECT * FROM Product";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("❌ Connection failed: " . $e->getMessage());
    }

    // Check if there are products or not
    if (empty($products)) {
        echo '<div style="text-align:center;padding:40px;font-size:22px;color:red;">❌ No products found.</div>';
    }
    
    // Parse the products and prepare the display
    $displayProducts = "";
    foreach ($products as $product) {
        $displayProducts .= '<div class="col-sm-6 col-md-4 col-lg-3 mb-4">';
        $displayProducts .= '  <div class="single-menu p-3" style="background:#fff;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,0.07);height:100%;">';
        $displayProducts .= '    <img class="img-fluid mb-2" src="img/menu-' . $product['Id'] . '.jpeg" alt="' . htmlspecialchars($product['Name']) . '" style="border-radius:8px;max-height:180px;object-fit:cover;width:100%;">';
        $displayProducts .= '    <h4 class="mt-2 mb-1" style="font-size:1.1rem;font-weight:600;color:#222;">' . htmlspecialchars($product['Name']) . '</h4>';
        $displayProducts .= '    <div class="mb-2" style="color:#28a745;font-weight:500;">$' . htmlspecialchars($product['Price']) . '</div>';
        $displayProducts .= '    <div class="mb-3" style="font-size:0.95rem;color:#666;min-height:40px;">' . htmlspecialchars($product['Description']) . '</div>';
        $displayProducts .= '    <form method="post" action="cart.php">';
        $displayProducts .= '      <input type="hidden" name="product_id" value="' . htmlspecialchars($product['Id']) . '">';
        $displayProducts .= '      <input type="hidden" name="product_quantity" value="1">'; // Default quantity
        $displayProducts .= '      <button type="submit" class="btn btn-primary btn-block">Add to Cart</button>';
        $displayProducts .= '    </form>';
        $displayProducts .= '  </div>';
        $displayProducts .= '</div>';
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
                                                <input type="text" class="form-control datetimepicker-input" name="time" placeholder="Time" data-target="#time" required="required"/>
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

        <!-- Categorie Menu Start -->
        <section id="categories-menu">
            <div class="container">
                <header class="section-header">
                    <h3>Categories</h3>
                </header>
                <div class="row">
                    <?= $displayCategories ?>
                </div>
            </div>
        </section>
        <!-- Categorie Menu End -->

        <!-- Menu Section Start -->
        <section id="food-menu">
            <div class="container">
                <header class="section-header">
                    <h3>delicious Food Menu</h3>
                </header>
                <div class="row">
                    <?= $displayProducts ?>
                </div>
            </div>
        </section>
        <!-- Menu Section End-->
         

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
