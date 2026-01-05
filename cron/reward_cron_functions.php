<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

function checkIfToday(mysqli $mysqli): ?array
{
    $today = date('Y-m-d');
    $stmt = $mysqli->prepare("SELECT id, date FROM rewards WHERE reward_sent = 0 AND date = ? ORDER BY date ASC LIMIT 1");
    $stmt->bind_param("s", $today);
    $stmt->execute();
    $result = $stmt->get_result();
    $reward = $result->fetch_assoc();
    $stmt->close();

    return $reward ?: null;
}

function findTheWinner(mysqli $mysqli): ?array
{
    $stmt = $mysqli->prepare("SELECT id, player_name, email, score, rewarded FROM players ORDER BY score DESC LIMIT 1");
    $stmt->execute();
    $result = $stmt->get_result();
    $winner = $result->fetch_assoc();
    $stmt->close();

    return $winner ?: null;
}

function sendEmail(string $address, string $subject, string $bodyMessage): bool
{
    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = 'smtp-relay.brevo.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = '9d7e25001@smtp-brevo.com';
        $mail->Password   = $_ENV['BREVO_PASS'];
        $mail->Port       = 2525;

        $mail->setFrom('no-reply@darwinsgym.eu', "Darwin's Gym");
        $mail->addAddress($address);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $bodyMessage;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mail error: " . $mail->ErrorInfo);
        return false;
    }
}

function processRewardTransaction(mysqli $mysqli, array $winner, array $reward): string
{
    try {
        $mysqli->begin_transaction();

        $stmt = $mysqli->prepare("UPDATE rewards SET player_id = ?, reward_sent = 1 WHERE id = ?");
        $stmt->bind_param("ss", $winner['id'], $reward['id']);
        $stmt->execute();
        $stmt->close();

        $newRewarded = (int)$winner['rewarded'] + 1;
        $stmt = $mysqli->prepare("UPDATE players SET rewarded = ? WHERE id = ?");
        $stmt->bind_param("is", $newRewarded, $winner['id']);
        $stmt->execute();
        $stmt->close();

        $mysqli->commit();

        return "Reward processed";
    } catch (Exception $e) {

        $mysqli->rollback();

        return "Server error";
    }
}
