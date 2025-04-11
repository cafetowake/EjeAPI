<?php
$host = '127.0.0.1';
$dbname = 'gdi';
$username = '';
$password = '';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;port=1806;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    die(json_encode([
        "status" => "error",
        "message" => "Error de conexión: " . $e->getMessage()
    ]));
}