<?php
$DB_HOST = '127.0.0.1';
$DB_NAME = 'ejeapi';
$DB_USER = 'paula';
$DB_PASS = '1806';

try {
    $pdo = new PDO("mysql:host=$DB_HOST;port=1806;dbname=$DB_NAME", $DB_USER, $DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Conexión exitosa a la base de datos.";
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
}
?>
