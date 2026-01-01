<?php

function validateNameAndToken(?string $player_name, ?string $token): bool
{
    return !empty($player_name) && !empty($token);
}

function getScore(mysqli $mysqli, ?string $player_name, ?string $token): ?int
{
    if (empty($player_name) || empty($token)) {
        return null;
    }

    $stmt = $mysqli->prepare("SELECT score FROM players WHERE player_name = ? AND session_token = ? AND session_expires_at > NOW() LIMIT 1");
    $stmt->bind_param('ss', $player_name, $token);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    if (!$result) return null;
    $score = (int)$result['score'];
    $stmt->close();
    return $score;
}

function scoreToJson(?int $score): array
{
    if ($score !== null) {
        return ['status' => 'success', 'score' => $score];
    } else {
        return ['status' => 'error'];
    }
}
