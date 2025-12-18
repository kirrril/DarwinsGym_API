<?php

function validateNameAndToken(?string $player_name, ?string $token): bool
{
    return !empty($player_name) && !empty($token);
}

function getPlayerId(mysqli $mysqli, ?string $player_name): ?int
{
    if (empty($player_name)) {
        return null;
    }

    $id_stmt = $mysqli->prepare("SELECT id FROM players WHERE player_name = ? LIMIT 1");
    $id_stmt->bind_param('s', $player_name);
    $id_stmt->execute();
    $result = $id_stmt->get_result();

    $player_id = ($result->num_rows > 0) ? (int)$result->fetch_assoc()['id'] : null;
    $id_stmt->close();

    return $player_id;
}

function checkTokenGetId(mysqli $mysqli, ?string $token): ?int
{
    if (empty($token)) {
        return null;
    }

    $token_stmt = $mysqli->prepare("SELECT player_id FROM sessions WHERE session_token = ? AND token_expires_at > NOW() LIMIT 1");
    $token_stmt->bind_param('s', $token);
    $token_stmt->execute();
    $result = $token_stmt->get_result();

    $stocked_id = ($result->num_rows > 0) ? (int)$result->fetch_assoc()['player_id'] : null;
    $token_stmt->close();

    return $stocked_id;
}

function checkPlayer($player_id, $stocked_id): ?bool
{
    if ($player_id === null || $stocked_id === null) {
        return null;
    }
    return $player_id === $stocked_id;
}

function getPlayerScore(mysqli $mysqli, ?int $player_id): ?int
{
    if ($player_id === null) {
        return null;
    }

    $score_stmt = $mysqli->prepare("SELECT score FROM scores WHERE player_id = ? LIMIT 1");
    $score_stmt->bind_param("i", $player_id);
    $score_stmt->execute();
    $result = $score_stmt->get_result();

    $score = ($result->num_rows > 0) ? (int)$result->fetch_assoc()['score'] : null;
    $score_stmt->close();
    return $score;
}

function getPlayerRank(mysqli $mysqli, ?int $player_score): ?int
{
    if ($player_score === null) {
        return null;
    }

    $rank_stmt = $mysqli->prepare("SELECT COUNT(*) + 1 AS player_rank FROM scores WHERE score > ?");
    $rank_stmt->bind_param("i", $player_score);
    $rank_stmt->execute();
    $result = $rank_stmt->get_result();
    $player_rank = (int)$result->fetch_assoc()['player_rank'];
    $rank_stmt->close();
    return $player_rank;
}

function rankToJson(?int $rank): array
{
    if ($rank > 0) {
        return (['status' => 'success', 'rank' => $rank]);
    }
    else
    {
        return (['status' => 'error']);
    }
}
