<?php
require_once 'config.php';
require_once 'reward_cron_functions.php';

$admin_email = "info@kirillukolov.com";

$status = "NOT_TODAY";

$reward = checkIfToday($mysqli);

if ($reward) {
    $winner = findTheWinner($mysqli);
    
    if ($winner) {
        $rewardTransactionResult = processRewardTransaction($mysqli, $winner, $reward);
        if ($rewardTransactionResult === "Reward processed") {
            $subject_winner = "Congratulations! You won this month's reward!";
            $bodyMessage_winner = "Congratulations! You won this month's reward!\nFill in the form below to choose color, size and design of your Alpha Male t-shirt!";
            if (sendEmail($winner['email'], $subject_winner, $bodyMessage_winner)) {
                $subject_admin = "{$reward['date']} reward processed";
                $bodyMessage_admin = "Mail sent to the winner: {$winner['email']}";
                $status = "REWARD_SENT";
            } else {
                $subject_admin = "Winner mail failed";
                $bodyMessage_admin = "Winner mail failed.\nCheck the database then contact: {$winner['email']}.";
                $status = "WINNER_MAIL_FAILED";
            }
        } else {
            $subject_admin = "! Server error !";
            $bodyMessage_admin = "Server error occured while processing {$reward['date']} reward.\nPlease check server activity.";
            $status = "SERVER_ERROR";
        }
        sendEmail($admin_email, $subject_admin, $bodyMessage_admin);
    } else {
        $subject_admin = "No winner found";
        $bodyMessage_admin = "No winner found, check the Darwin's Gym database";
        sendEmail($admin_email, $subject_admin, $bodyMessage_admin);
        $status = "NO_WINNER";
    }
}

echo $status;

if (PHP_SAPI !== 'cli' || !isset($GLOBALS['__TEST_INPUT__'])) {
    $mysqli->close();
}
