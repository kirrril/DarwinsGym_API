<?php
header("Content-Type: application/json");
require_once 'config.php';

$page = max(0, (int)($_GET['page'] ?? '0'));
$desc = ($_GET['is_desc'] ?? 'true') === 'true';
$limit = 10;
$offset = $page * $limit;
$order = $desc ? 'DESC' : 'ASC';

$page_stmt = $mysqli->prepare("SELECT player_name, score, score_updated_at FROM players ORDER BY score $order, score_updated_at $order LIMIT ? OFFSET ?");
$page_stmt->bind_param("ii", $limit, $offset);
$page_stmt->execute();
$page_result = $page_stmt->get_result();

$players = [];

while ($row = $page_result->fetch_assoc())
{
    $players[] = [
        "player_name" => $row['player_name'],
        "score" => (int)$row['score'],
        "date" => date('Y-m-d', strtotime($row['score_updated_at']))
    ];
}
echo json_encode(["players" => $players]);
$page_stmt->close();
$mysqli->close();
?>
