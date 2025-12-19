<?php

require_once 'config.php';
require_once 'check_verified_functions.php';

header('Content-Type: application/json');

$username = $_GET['username'] ?? null;

if (validateUsername($username)) {
    $status = getEmailVerificationStatus($mysqli, $username);
}
else
{
    $status = null;
}

echo json_encode(statusToJson($status));