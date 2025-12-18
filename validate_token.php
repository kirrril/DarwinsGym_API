<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
require_once 'config.php';

$token = $_GET['token'] ?? '';
if (empty($token)) {
    echo json_encode(["status" => "error", "message" => "No token"]);
    exit;
}

$stmt1 = $mysqli->prepare("SELECT player_id FROM sessions WHERE session_token = ? AND token_expires_at > NOW() LIMIT 1");
$stmt1->bind_param("s", $token);
$stmt1->execute();
$result1 = $stmt1->get_result();

if ($result1->num_rows !== 1) {
    echo json_encode(["status" => "error", "message" => "Invalid or expired token"]);
    $stmt1->close();
    exit;
}

$row = $result1->fetch_assoc();
$player_id = $row['player_id'];
$stmt1->close();

$stmt2 = $mysqli->prepare("SELECT player_name FROM players WHERE id = ? LIMIT 1");
$stmt2->bind_param("i", $player_id);
$stmt2->execute();
$result2 = $stmt2->get_result();

if ($result2->num_rows === 1) {
    $row2 = $result2->fetch_assoc();
    echo json_encode([
        "status" => "success",
        "player_name" => $row2['player_name']
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "Player not found"]);
}

$stmt2->close();
$mysqli->close();
?>
