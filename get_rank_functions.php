<?php

function validateNameAndToken(?string $player_name, ?string $token): bool
{
    return !empty($player_name) && !empty($token);
}

function getScoreData(mysqli $mysqli, ?string $player_name, ?string $token): ?array
{
    if (empty($player_name) || empty($token)) {
        return null;
    }

    $id_stmt = $mysqli->prepare("SELECT score, score_updated_at FROM players WHERE player_name = ? AND session_token = ? AND session_expires_at > NOW() LIMIT 1");
    $id_stmt->bind_param('ss', $player_name, $token);
    $id_stmt->execute();
    $result = $id_stmt->get_result();
    $score_data = $result->fetch_assoc();
    $id_stmt->close();
    return $score_data ?: null;
}

function getRank(mysqli $mysqli, array $score_data): int
{
    $rank_stmt = $mysqli->prepare("SELECT COUNT(*) + 1 AS player_rank FROM players WHERE score > ? OR (score = ? AND score_updated_at < ?)");
    $rank_stmt->bind_param("iis", $score_data['score'], $score_data['score'], $score_data['score_updated_at']);
    $rank_stmt->execute();
    $result = $rank_stmt->get_result();
    $player_rank = (int)$result->fetch_assoc()['player_rank'];
    $rank_stmt->close();
    return $player_rank;
}

function rankToJson(?int $player_rank): array
{
    if ($player_rank > 0) {
        return ['status' => 'success', 'rank' => $player_rank];
    }
    else
    {
        return ['status' => 'error'];
    }
}
