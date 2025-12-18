<?php
header("Content-Type: application/json");
require_once 'config.php';

$data = json_decode(file_get_contents("php://input"), true);
$player_name = $data['player_name'] ?? '';
$token = $data['token'] ?? '';
$new_score = (int)($data['score'] ?? 0);

if (empty($player_name) || empty($new_score) || empty($token)) {
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
    echo json_encode(["status" => "error", "message" => "Invalid or expired token"]);
    $token_stmt->close();
    exit;
}

$token_player_id = (int)$result->fetch_assoc()['player_id'];
$token_stmt->close();

if ($token_player_id !== $player_id){
    echo json_encode(["status" => "error", "message" => "Invalid session"]);
    exit;
}

$stmt = $mysqli->prepare("UPDATE scores SET score = GREATEST(score, ?), score_updated_at = NOW() WHERE player_id = ?");
$stmt->bind_param("is", $new_score, $player_id);
$stmt->execute();
echo json_encode(["status" => "success", "message" => "Score updated"]);
$stmt->close();
$mysqli->close();
?>
