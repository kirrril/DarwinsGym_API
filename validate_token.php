<?php
header("Access-Control-Allow-Origin: *"); // Supprimer en prod
header("Content-Type: application/json");
require_once 'config.php';

$data = json_decode(file_get_contents("php://input"), true);
$token = trim($data['token'] ?? '');

if (empty($token)) {
    echo json_encode(["status" => "error", "message" => "No token"]);
    exit;
}

$stmt = $mysqli->prepare("SELECT player_name FROM players WHERE session_token = ? AND session_expires_at > NOW() LIMIT 1");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo json_encode(["status" => "error", "message" => "Invalid or expired token"]);
    $stmt->close();
    exit;
}

$player_name = $result->fetch_assoc()['player_name'];
$stmt->close();

echo json_encode(["status" => "success", "player_name" => $player_name]);

$mysqli->close();
?>
