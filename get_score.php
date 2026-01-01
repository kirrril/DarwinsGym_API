<?php
header("Content-Type: application/json");
require_once 'config.php';
require_once 'get_highest_score_functions.php';

$data = json_decode(file_get_contents("php://input"), true);

$player_name = trim($data['player_name'] ?? '');
$token = trim($data['token'] ?? '');

if (!validateNameAndToken($player_name, $token)){
    $score = null;
} else {
    $score = getScore($mysqli, $player_name, $token);
}
$mysqli->close();

echo json_encode(scoreToJson($score));