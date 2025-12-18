<?php
header("Content-Type: application/json");
require_once 'config.php';

$stmt = $mysqli->prepare("
    SELECT players.player_name, scores.score, scores.score_updated_at
    FROM scores
    JOIN players ON scores.player_id = players.id
    WHERE YEAR(score_updated_at) = YEAR(CURDATE())
    AND MONTH(score_updated_at) = MONTH(CURDATE())
    ORDER BY scores.score DESC, scores.score_updated_at DESC
    LIMIT 10
");
$stmt->execute();
$result = $stmt->get_result();

$players = [];
while ($row = $result->fetch_assoc()) {
    $players[] = [
        "player_name" => $row['player_name'],
        "score"       => (int)$row['score'],
        "date"        => date('Y-m-d', strtotime($row['score_updated_at']))
    ];
}
$stmt->close();

echo json_encode(["players" => $players]);
?>
