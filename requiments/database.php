<?php
$servername = "hostname";
$username = "username";
$password = "password";
$dbname = "dbname";
$encryptionKey = "8gthkl77335628TvK21030ggvnbc";

if (!function_exists('executeQuery')) {
    function executeQuery($sql, $params = []) {
        global $conn;
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Erreur lors de l'exécution de la requête : " . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('encryptData')) {
    function encryptData($data, $key) {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encryptedData = openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv);
        return base64_encode($iv . $encryptedData);
    }
}

if (!function_exists('sanitizeInput')) {
    function sanitizeInput($input) {
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('validateInput')) {
    function validateInput($input) {
    }
}

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (!$conn) {
        die("Erreur de connexion à la base de données.");
    }
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>
