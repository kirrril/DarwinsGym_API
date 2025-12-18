<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
require_once 'config.php';
require_once 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

$data = json_decode(file_get_contents("php://input"), true);

$username = trim($data['username'] ?? '');
$email    = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if (strlen($username) < 3 || strlen($username) > 20 || !preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
    echo json_encode(["status" => "error", "message" => "Invalid username"]);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["status" => "error", "message" => "Invalid email"]);
    exit;
}
if (strlen($password) < 8) {
    echo json_encode(["status" => "error", "message" => "Password too short"]);
    exit;
}

$stmt = $mysqli->prepare("SELECT id FROM players WHERE player_name = ? OR email = ?");
$stmt->bind_param("ss", $username, $email);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "Username or email already taken"]);
    $stmt->close();
    exit;
}
$stmt->close();

$hash  = password_hash($password, PASSWORD_BCRYPT);
$token = bin2hex(random_bytes(32));

$stmt = $mysqli->prepare("INSERT INTO players (player_name, email, password_hash, verification_token) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $username, $email, $hash, $token);
$stmt->execute();
$player_id = $mysqli->insert_id;
$stmt->close();

$stmt = $mysqli->prepare("INSERT INTO scores (player_id, score) VALUES (?, 0)");
$stmt->bind_param("i", $player_id);
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
    $mail->Subject = "Darwin's Gym – Verify your email";
    $mail->Body    = "<h2>Welcome $username!</h2><p><a href='https://darwinsgym.eu/verify.php?token=$token'>Verify my email</a></p>";

    $mail->send();
    error_log("BREVO MAIL ENVOYÉ À $email !");
} catch (Exception $e) {
    error_log("Brevo error: " . $mail->ErrorInfo);
}

echo json_encode(["status" => "success", "message" => "Account created! Check your email."]);
$mysqli->close();
?>
