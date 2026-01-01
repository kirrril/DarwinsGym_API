<?php
header("Content-Type: application/json");
require_once 'config.php';
require_once 'get_rank_functions.php';

$rawInput = $GLOBALS['__TEST_INPUT__'] ?? file_get_contents("php://input");
$data = json_decode($rawInput, true);

$player_name = trim($data['player_name'] ?? '');
$token = trim($data['token'] ?? '');

if (!validateNameAndToken($player_name, $token)) {
    $rank = null;
} else {
    $score_data = getScoreData($mysqli, $player_name, $token);
    $rank = $score_data ? getRank($mysqli, $score_data) : null;
}
$mysqli->close();

echo json_encode(rankToJson($rank));
