<?php 

$dotenv = parse_ini_file(__DIR__ . '/../.env');

$host = $dotenv['DB_HOST'];
$port = $dotenv['DB_PORT'];
$db   = $dotenv['DB_NAME'];
$user = $dotenv['DB_USER'];
$pass = $dotenv['DB_PASS'];


try {
    $connection = new PDO("pgsql:host=$host;port=$port;dbname=$db", $user, $pass);
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

?>