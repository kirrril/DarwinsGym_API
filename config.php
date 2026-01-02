<?php
require_once __DIR__ . '/vendor/autoload.php';

if (file_exists(__DIR__ . '/.env')) {
  $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
  $dotenv->safeLoad();
}

$host = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?? 'localhost';
$user = $_ENV['DB_USER'] ?? getenv('DB_USER') ?? 'root';
$pass = $_ENV['DB_PASS'] ?? getenv('DB_PASS') ?? '';
$db   = $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?? 'database';

$mysqli = new mysqli($host, $user, $pass, $db);

if ($mysqli->connect_error) {
  die("Connection error: " . $mysqli->connect_error);
}

return $mysqli;
