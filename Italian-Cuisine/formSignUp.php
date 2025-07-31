<?php
session_start();
    require 'functions.php';
    global $IDINCREMET;
    $password1 = $_POST['password1'];
    $password2 = $_POST['password2'];
    if ($password1 !== $password2) {
        die("Error: the first password is different than the second password: $password1 is not $password2");
    };
    $phoneNb = $_POST['phoneNb'];
    if (!preg_match('/^\d{2}\d{3}\d{3}$/', $phoneNb)) {
        die("Error: the phone number is not valid: $phoneNb");
    }
       
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $gmail = $_POST['email'];
    $address = $_POST['address'];
    $location = $_POST['location'];
    $birthday = $_POST['birthday'];
    
    
    echo "Row 1: $firstName, $lastName.";
    echo "<br>";
    echo "Row 2: $phoneNb, $gmail.";
    echo "<br>";
    echo "Row 3: $address, $location.";
    echo "<br>";
    echo "Row 4: $birthday.";
    echo "<br>";
    echo "Row 5: $password1, $password2.";
    echo "<br>";



    $conn = getPDOConnection();
    if ($conn) {
        echo "connected<br>";
    } else {
        die();
    }

    try {
        $sql = "INSERT 
                INTO account(FirstName ,LastName, PhoneNb ,Address ,Location ,Birthday ,Gmail ,Password ) 
                VALUES(?,?,?,?,?,?,?,?);";
        $stmt = $conn->prepare($sql);
        // If you're not specifying the ID manually, and it's auto-incrementing, use NULL
       

        $stmt->bindParam(1, $firstName);
        $stmt->bindParam(2, $lastName);
        $stmt->bindParam(3, $phoneNb);
        $stmt->bindParam(4, $address);
        $stmt->bindParam(5, $location);
        $stmt->bindParam(6, $birthday);
        $stmt->bindParam(7, $gmail);
        $stmt->bindParam(8, $password1);

        // Execute the insert
if ($stmt->execute()) {
    echo "Account created successfully.";

    // insert the new account into the client table with the provided data
    $sql = "INSERT INTO client(AccountId) VALUES(LAST_INSERT_ID())";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    echo "Client account created successfully.";

    // Set session variables
    $_SESSION['gmail'] = $gmail;
    $_SESSION['accountType'] = $result[0]['AccountType'] ?? 'Client';
    $_SESSION['id'] = $result[0]['Id'];
    header("Location: profile.php");
    exit();
} else {
    echo "Error: " . $stmt->errorCode();
    echo "Error: " . $stmt->errorInfo();
}
       

    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    } finally {
        $stmt = null;
        $conn = null;
    };
?>