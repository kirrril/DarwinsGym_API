<?php

function validateUsername(?string $username): bool
{
    return !empty($username);
}

function getEmailVerificationStatus(mysqli $mysqli, string $username): ?bool
{
    $stmt = $mysqli->prepare(
        "SELECT email_verified FROM players WHERE player_name = ?"
    );
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$row) {
        return null;
    }

    return (bool) $row['email_verified'];
}

function statusToJson(?bool $status): array
{
    if ($status === true) {
        return (['status' => 'verified']);
    }
    else if ($status === false)
    {
        return (['status' => 'pending']);
    }
    else
    {
        return (['status' => 'error']);
    }
}