<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
require_once 'config.php';
require_once __DIR__ . '/vendor/autoload.php';

if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

if (PHP_SAPI === 'cli' && isset($GLOBALS['__TEST_INPUT__'])) {
    $data = json_decode($GLOBALS['__TEST_INPUT__'], true);
} else {
    $data = json_decode(file_get_contents("php://input"), true);
}

$player_name = trim($data['player_name'] ?? '');
$email    = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if (strlen($player_name) < 3 || strlen($player_name) > 20 || !preg_match('/^[a-zA-Z0-9_-]+$/', $player_name)) {
    echo json_encode(["status" => "error", "message" => "Invalid username"]);

    if (PHP_SAPI !== 'cli' || !isset($GLOBALS['__TEST_INPUT__'])) {
        exit;
    }
    return;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["status" => "error", "message" => "Invalid email"]);

    if (PHP_SAPI !== 'cli' || !isset($GLOBALS['__TEST_INPUT__'])) {
        exit;
    }
    return;
}

if (strlen($password) < 8) {
    echo json_encode(["status" => "error", "message" => "Password too short"]);

    if (PHP_SAPI !== 'cli' || !isset($GLOBALS['__TEST_INPUT__'])) {
        exit;
    }
    return;
}

$stmt = $mysqli->prepare("SELECT id FROM players WHERE player_name = ? OR email = ?");
$stmt->bind_param("ss", $player_name, $email);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "Username or email already taken"]);
    $stmt->close();

    if (PHP_SAPI !== 'cli' || !isset($GLOBALS['__TEST_INPUT__'])) {
        exit;
    }
    return;
}
$stmt->close();

$hash  = password_hash($password, PASSWORD_BCRYPT);
$token = bin2hex(random_bytes(32));

$stmt = $mysqli->prepare("INSERT INTO players (id, player_name, email, password_hash, verif_token, score) VALUES (UUID(), ?, ?, ?, ?, 0)");
$stmt->bind_param("ssss", $player_name, $email, $hash, $token);
$stmt->execute();
$stmt->close();

$mail = new PHPMailer(true);
$brevo_pass = $_ENV['BREVO_PASS'] ?? '';
try {
    $mail->isSMTP();
    $mail->Host       = 'smtp-relay.brevo.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = '9d7e25001@smtp-brevo.com';
    $mail->Password   = $brevo_pass;
    $mail->SMTPSecure = '';
    $mail->Port       = 2525;

    $mail->setFrom('no-reply@darwinsgym.eu', "Darwin's Gym");
    $mail->addAddress($email);
    $mail->isHTML(true);
    $mail->Subject = "Darwin's Gym _ Verify your email";
    $mail->Body    = "<h2>Welcome $player_name!</h2><p><a href='https://darwinsgym.eu/verify_mail.php?token=$token'>Verify my email</a></p>";

    $mail->send();
    error_log("BREVO MAIL ENVOYÉ À $email !");
} catch (Exception $e) {
    error_log("Brevo error: " . $mail->ErrorInfo);
}

echo json_encode(["status" => "success", "message" => "Account created! Check your email."]);

if (PHP_SAPI !== 'cli' || !isset($GLOBALS['__TEST_INPUT__'])) {
    $mysqli->close();
}
