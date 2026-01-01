<?php
header("Access-Control-Allow-Origin: *"); // Supprimer en prod
header("Content-Type: application/json");
require_once 'config.php';
require_once 'login_functions.php';

$rawInput = $GLOBALS['__TEST_INPUT__'] ?? file_get_contents("php://input");
$data = json_decode($rawInput, true);
$player_name = trim($data['player_name'] ?? '');
$password = $data['password'] ?? '';
$token = bin2hex(random_bytes(32));
$expires = date('Y-m-d H:i:s', strtotime('+30 days'));

if (!validatePlayerName($player_name) || !validatePassword($mysqli, $player_name, $password)) {
    $token = null;
    $message = "Invalid credentials";
} elseif (!validateEmail($mysqli, $player_name)) {
    $token = null;
    $message = "Email not verified";
} elseif (!insertSessionTokenAndExpiryDate($mysqli, $player_name, $token, $expires)) {
    $token = null;
    $message = "Server error";
} else {
    $message = "Successfully logged in!";
}
echo json_encode(tokenToJson($token, $message));
