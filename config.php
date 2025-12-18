<?php
require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$host = $_ENV['DB_HOST'] ?? 'localhost';
$user = $_ENV['DB_USER'] ?? 'root';
$pass = $_ENV['DB_PASS'] ?? '';
$db   = $_ENV['DB_NAME'] ?? 'database';

$mysqli = new mysqli($host, $user, $pass, $db);

if ($mysqli->connect_error) {
  die("Connection error: " . $mysqli->connect_error);
}

return $mysqli;
?>