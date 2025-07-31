<?php
require "functions.php";
// Italian-Cuisine/contact_process.php

$conn = getPDOConnection(); 
if (!$conn) {
    die("❌ Connection failed.");
}

try {

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $subject = $_POST['subject'] ?? '';
        $message = $_POST['message'] ?? '';

        if ($name && $email && $subject && $message) {
            $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $subject, $message]);
            echo '<div style="text-align:center;padding:40px;font-size:22px;color:green;">
                ✅ Thank you, <b>' . htmlspecialchars($name) . '</b>!<br>
                Your message has been sent successfully.<br>
                You will be redirected in 3 seconds...
            </div>';
            echo '<script>
                setTimeout(function(){
                    window.history.back();
                }, 3000);
            </script>';
            exit;
        } else {
            echo '<div style="text-align:center;padding:40px;font-size:22px;color:red;">❌ Please fill all fields.</div>';
            echo '<script>
                setTimeout(function(){
                    window.history.back();
                }, 2000);
            </script>';
            exit;
        }
    }
} catch (PDOException $e) {
    echo "❌ Connection failed: " . $e->getMessage();
}
?>