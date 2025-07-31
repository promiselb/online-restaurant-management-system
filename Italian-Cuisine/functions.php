<?php

    function getPDOConnection(): PDO|null {
        $servername = "127.0.0.1";
        $username = "root";
        $password = "";
        $dbname = "project2";
        try {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            return null;
        }
    }


    function getAdminSecretKeys(): array {
        $conn = getPDOConnection();
        if (!$conn) {
            return []; // return empty if connection fails
        }

        try {
            $stmt = $conn->query("SELECT SecretKey  FROM AdminSecretKey");
            $keys = $stmt->fetchAll(PDO::FETCH_COLUMN);
            return $keys ?: [];
        } catch (PDOException $e) {
            // Log error or display a message if needed
            return [];
        } finally {
            $stmt = null;
            $conn = null;
        }
    }

    // Function to check if an admin secret key (ask) is valid
    // is within the ASK_Array
    function addAdminSecretKey($ask): bool {
        $conn = getPDOConnection();
        if (!$conn) return false;
        try {
            $stmt = $conn->prepare("INSERT INTO AdminSecretKey (SecretKey) VALUES (:key)");
            $stmt->execute(['key' => $ask]);
            return true;
        } catch (PDOException $e) {
            return false; // key already exists or error
        }
    }

    function isAdminSecretKeyExists($ask): bool {
        $conn = getPDOConnection();
        if (!$conn) return false;
        $stmt = $conn->prepare("SELECT 1 FROM AdminSecretKey WHERE SecretKey = :key");
        $stmt->execute(['key' => $ask]);
        return $stmt->fetchColumn() !== false;
    }

    function removeAdminSecretKey($ask): bool {
        $conn = getPDOConnection();
        if (!$conn) return false;
        $stmt = $conn->prepare("DELETE FROM AdminSecretKey WHERE SecretKey = :key");
        return $stmt->execute(['key' => $ask]);
    }
    
    function generateAdminSecretKey(): string {
        // Generate a new admin secret key
        // a hard secert key is a good practice
        $length = 10; // Length of the secret key
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
?>