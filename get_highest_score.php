<?php
header("Content-Type: application/json");
require_once 'config.php';

$player_name = $_GET['player_name'] ?? '';
$token = $_GET['token'] ?? '';

if (empty($player_name) || empty($token)) {
    echo json_encode(["status" => "error", "message" => "Missing data"]);
    exit;
}

$id_stmt = $mysqli->prepare("SELECT id FROM players WHERE player_name = ? LIMIT 1");
$id_stmt->bind_param('s', $player_name);
$id_stmt->execute();
$result = $id_stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Unknown player"]);
    $id_stmt->close();
    exit;
}

$player_id = (int)$result->fetch_assoc()['id'];
$id_stmt->close();

$token_stmt = $mysqli->prepare("SELECT player_id FROM sessions WHERE session_token = ? AND token_expires_at > NOW() LIMIT 1");
$token_stmt->bind_param('s', $token);
$token_stmt->execute();
$result = $token_stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Invalid session"]);
    $token_stmt->close();
    exit;
}

$stocked_id = (int)$result->fetch_assoc()['player_id'];

if ($player_id !== $stocked_id){
    echo json_encode(["status" => "error", "message" => "Invalid session"]);
    $token_stmt->close();
    exit;
}
$token_stmt->close();

$score_stmt= $mysqli->prepare("SELECT score FROM scores WHERE player_id = ? LIMIT 1");
$score_stmt->bind_param("i", $player_id);
$score_stmt->execute();
$result = $score_stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "No score found"]);
    $score_stmt->close();
    exit;
}

$player_score = (int)$result->fetch_assoc()['score'];
$score_stmt->close();

echo json_encode([
    "status" => "success",
    "score" => $player_score
]);

$mysqli->close();
?>
