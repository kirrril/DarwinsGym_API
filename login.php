<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
require_once 'config.php';

$data = json_decode(file_get_contents("php://input"), true);
$username = trim($data['username'] ?? '');
$password = $data['password'] ?? '';

if (empty($username) || empty($password)) {
    echo json_encode(["status" => "error", "message" => "Missing credentials"]);
    exit;
}

$stmt = $mysqli->prepare("SELECT id, password_hash, email_verified FROM players WHERE player_name = ? LIMIT 1");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Invalid credentials"]);
    $stmt->close();
    exit;
}

$user = $result->fetch_assoc();
$stmt->close();

if (!password_verify($password, $user['password_hash'])) {
    echo json_encode(["status" => "error", "message" => "Invalid credentials"]);
    exit;
}

if (!$user['email_verified']) {
    echo json_encode(["status" => "error", "message" => "Email not verified"]);
    exit;
}

$token = bin2hex(random_bytes(32));
$expires = date('Y-m-d H:i:s', strtotime('+30 days'));

$stmt = $mysqli->prepare("INSERT INTO sessions (player_id, session_token, token_expires_at) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $user['id'], $token, $expires);
$stmt->execute();
$stmt->close();

echo json_encode([
    "status" => "success",
    "session_token" => $token,
    "player_name" => $username
]);
?>
