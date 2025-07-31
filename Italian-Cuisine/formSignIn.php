<?php
    session_start();
    require "functions.php";
    $gmail = $_POST['email'];
    $password = $_POST['password'];
    echo "Identifiants reçus: $gmail, $password";


    $conn = getPDOConnection();
    try {
        $sql = "SELECT * from account where Gmail = :gmail AND Password = :password;";
        // var_dump($sql);
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':gmail', $gmail);
        $stmt->bindParam(':password', $password);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        var_dump($result);
        if ($result) {
            $_SESSION['gmail'] = $gmail;
            $_SESSION['accountType'] = $result[0]['AccountType'] ?? 'Client';
            $_SESSION['id'] = $result[0]['Id'];

            // Redirect to profile page or another page
            header("Location: profile.php");
        
        } else {
            echo "No matching client found.";
        }
        
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    } finally {
        $stmt = null;
        $conn = null;
    }
?>
  