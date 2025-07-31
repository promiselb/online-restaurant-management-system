<?php
    session_start();
    require 'functions.php';

    if (!isset($_SESSION['gmail'])) {
        // echo a link which redirects to the login page
        echo '<a href="login.php">Click here to login</a>';
        die("You must be logged in to access this page.");       
    }

    // Check if the user is an admin
    if (!isset($_SESSION['accountType']) || $_SESSION['accountType'] !== 'Admin') {
        // echo a link which redirects to the profile page
        echo '<a href="profile.php">Click here to go to your profile</a>';
        die("You do not have permission to access this page.");
       
    }

    // get the admin secret key from the GET request
    $ask = $_GET['key'] ?? '';
    if (empty($ask)) {
        die("Error: No admin secret key provided.");
    }
    
    if (!isAdminSecretKeyExists($ask)) {
        die("Error: Invalid admin secret key.");
    }
    // remove the admin secret key
    removeAdminSecretKey($ask);

    header("Location: admin.php?newKey=" . urlencode($newAdminKey));
    exit();
?>