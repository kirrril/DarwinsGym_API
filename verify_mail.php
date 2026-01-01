<?php
require_once 'config.php';
$token = $_GET['token'] ?? '';

if (empty($token)) {
    echo "<h1>Invalid link.</h1>";
    exit;
}

$stmt = $mysqli->prepare("UPDATE players SET email_verified = 1, verif_token = NULL WHERE verif_token = ? AND email_verified = 0 LIMIT 1");
$stmt->bind_param("s", $token);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "<h1>Email verified! You can now log in.</h1>";
} else {
    echo "<h1>Invalid or expired link.</h1>";
}

$stmt->close();
$mysqli->close();
?>
