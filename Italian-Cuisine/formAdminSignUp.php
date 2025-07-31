<?php
    session_start();
    require 'functions.php';

    $password1 = $_POST['password1'];
    $password2 = $_POST['password2'];

    if ($password1 !== $password2) {
        die("Error: Passwords do not match.");
    }

    $phoneNb = $_POST['phoneNb'];
    if (!preg_match('/^\d{2}\d{3}\d{3}$/', $phoneNb)) {
        die("Error: Invalid phone number format.");
    }

    $ask = $_POST['ask'];
    if (isAdminSecretKeyExists($ask)) {
        die("Error: Invalid admin secret key.");
    } else {
        removeAdminSecretKey($ask); // Prevent reuse
    }

    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $gmail = $_POST['email'];
    $address = $_POST['address'];
    $location = $_POST['location'];
    $birthday = $_POST['birthday'];
    $salary = 5000.00; // default salary for new admins

    $conn = getPDOConnection();
    if (!$conn) {
        die("Database connection failed.");
    }

    try {
        // Check if the Gmail already exists
        $check = $conn->prepare("SELECT Id FROM Account WHERE Gmail = ?");
        $check->execute([$gmail]);
        if ($check->fetch()) {
            die("Error: An account with this email already exists.");
        }

        // Insert into Account table with AccountType = 'Admin'
        $stmt = $conn->prepare("
            INSERT INTO Account 
                (FirstName, LastName, PhoneNb, Address, Location, Birthday, Gmail, Password, AccountType)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Admin')
        ");

        $stmt->execute([
            $firstName,
            $lastName,
            $phoneNb,
            $address,
            $location,
            $birthday,
            $gmail,
            $password1
        ]);

        $accountId = $conn->lastInsertId(); // Get newly created account ID

        // Insert into Employee
        $stmtEmp = $conn->prepare("INSERT INTO Employee (AccountId, Salary) VALUES (?, ?)");
        $stmtEmp->execute([$accountId, $salary]);

        // Insert into Admin
        $stmtAdmin = $conn->prepare("INSERT INTO Admin (AccountId) VALUES (?)");
        $stmtAdmin->execute([$accountId]);

        // Login the user
        $_SESSION['gmail'] = $gmail;
        $_SESSION['accountType'] = $result[0]['AccountType'] ?? 'Client';
        $_SESSION['id'] = $result[0]['Id'];
        header("Location: admin.php");
        exit();

    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    } finally {
        $conn = null;
    }
?>
