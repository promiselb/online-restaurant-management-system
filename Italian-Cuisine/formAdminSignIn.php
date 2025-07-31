<?php
session_start();
require "functions.php";

$gmail = $_POST['email'];
$password = $_POST['password'];

$conn = getPDOConnection();

try {
    $sql = "SELECT a.*
            FROM Account a
            JOIN Admin ad ON a.Id = ad.AccountId
            WHERE a.Gmail = :gmail
              AND a.Password = :password
              AND a.AccountType = 'Admin'
            LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':gmail', $gmail);
    $stmt->bindParam(':password', $password);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $_SESSION['gmail'] = $gmail;
        $_SESSION['accountType'] = $result['AccountType'] ?? 'Client';
        $_SESSION['id'] = $result['Id'];
        header("Location: admin.php");
        exit;
    } else {
        echo "❌ Invalid admin credentials.";
    }

} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage();
} finally {
    $stmt = null;
    $conn = null;
}
?>
