<?php

function validatePlayerName(?string $player_name): bool
{
    return !empty($player_name);
}

function validatePassword(mysqli $mysqli, ?string $player_name, ?string $password): bool
{
    $stmt = $mysqli->prepare("SELECT password_hash FROM players WHERE player_name = ? LIMIT 1");
    $stmt->bind_param("s", $player_name);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    if (!$result) {
        return false;
    }
    return password_verify($password, $result['password_hash']);
}

function validateEmail(mysqli $mysqli, ?string $player_name): bool
{
    $stmt = $mysqli->prepare("SELECT email_verified FROM players WHERE player_name = ? LIMIT 1");
    $stmt->bind_param("s", $player_name);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    if (!$result) {
        return false;
    }
    return (bool) $result['email_verified'];
}

function insertSessionTokenAndExpiryDate(mysqli $mysqli, string $player_name, string $token, string $expires): bool
{
    $stmt = $mysqli->prepare("UPDATE players SET session_token = ?, session_expires_at = ? WHERE player_name = ? LIMIT 1");
    $stmt->bind_param("sss", $token, $expires, $player_name);
    $success = $stmt->execute();;
    $stmt->close();
    return $success;
}


function tokenToJson(?string $token, ?string $message): array
{
    if ($token !== null) {
        return ['status' => 'success', 'message' => $message, 'session_token' => $token];
    } else {
        return ['status' => 'error', 'message' => $message];
    }
}
