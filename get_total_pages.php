<?php
header("Content-Type: application/json");
require_once 'config.php';

$result = $mysqli->query("SELECT COUNT(*) AS count FROM players")->fetch_row();
$total_pages = (int)ceil($result[0] / 10);

echo json_encode(["total_pages" => $total_pages]);
?>
