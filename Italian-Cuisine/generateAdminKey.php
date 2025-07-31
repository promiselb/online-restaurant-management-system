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

    // Generate a new admin secret key
    $newAdminKey = generateAdminSecretKey();  
    if (isAdminSecretKeyExists($newAdminKey)) {
        // echo a link which redirects to the admin dashboard
        echo '<a href="admin.php">Click here to go to the admin dashboard</a>';
        die("Failed to generate a new admin secret key.");
    }
    addAdminSecretKey($newAdminKey);
    // redirect to the admin dashboard with the new key
    header("Location: admin.php?newKey=" . urlencode($newAdminKey));
    exit();
?>