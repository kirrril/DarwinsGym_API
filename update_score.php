<?php
header("Content-Type: application/json");
require_once 'config.php';

if (PHP_SAPI === 'cli' && isset($GLOBALS['__TEST_INPUT__'])) {
    $data = json_decode($GLOBALS['__TEST_INPUT__'], true);
} else {
    $data = json_decode(file_get_contents("php://input"), true);
}

$player_name = $data['player_name'] ?? '';
$token = $data['token'] ?? '';
$new_score = (int)($data['score'] ?? 0);

if (empty($player_name) || empty($new_score) || empty($token)) {
    echo json_encode(["status" => "error", "message" => "Missing data"]);
    exit;
}

$stmt = $mysqli->prepare("UPDATE players SET score = GREATEST(score, ?), score_updated_at = NOW() WHERE player_name = ? AND session_token = ? AND score < ?");
$stmt->bind_param("issi", $new_score, $player_name, $token, $new_score);
$stmt->execute();

if ($stmt->affected_rows === 1) {
    echo json_encode(["status" => "success", "message" => "Score updated"]);
} else {
    echo json_encode(["status" => "success", "message" => "Score not improved"]);
}

$stmt->close();

if (PHP_SAPI !== 'cli' || !isset($GLOBALS['__TEST_INPUT__'])) {
    $mysqli->close();
}

