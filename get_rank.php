<?php
header("Content-Type: application/json");
require_once 'config.php';
require_once 'get_rank_functions.php';

$player_name = $_GET['player_name'] ?? '';
$token = $_GET['token'] ?? '';

if (!validateNameAndToken($player_name, $token)) {
    $rank = null;
} else {
    $player_id = getPlayerId($mysqli, $player_name);
    $stocked_id = checkTokenGetId($mysqli, $token);
    if (!checkPlayer($player_id, $stocked_id)) {
        $rank = null;
    } else {
        $score = getPlayerScore($mysqli, $player_id);
        $rank = getPlayerRank($mysqli, $score);
    }
}
$mysqli->close();

echo json_encode(rankToJson($rank));
