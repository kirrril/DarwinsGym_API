<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class RewardCronTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        require_once __DIR__ . '/../cron/reward_cron_functions.php';
    }

    public function testNotToday(): void
    {
        require __DIR__ . '/../config.php';

        $mysqli->begin_transaction();

        $today = date('Y-m-d');
        $tomorrow = date('Y-m-d', strtotime($today . ' +1 day'));
        $stmt = $mysqli->prepare("UPDATE rewards SET date = ? WHERE date = ?");
        $stmt->bind_param("ss", $tomorrow, $today);
        $stmt->execute();
        $stmt->close();

        $this->assertNull(checkIfToday($mysqli));

        $mysqli->rollback();
    }

    public function testToday(): void
    {
        require __DIR__ . '/../config.php';

        $mysqli->begin_transaction();

        $today = date('Y-m-d');
        $stmt = $mysqli->prepare("SELECT id FROM rewards WHERE reward_sent = 0 ORDER BY date ASC LIMIT 1");
        $stmt->execute();
        $result = $stmt->get_result();
        $rewardId = $result->fetch_assoc();
        $stmt->close();

        $stmt = $mysqli->prepare("UPDATE rewards SET date = ? WHERE id = ?");
        $stmt->bind_param("ss", $today, $rewardId['id']);
        $stmt->execute();
        $stmt->close();

        $reward = checkIfToday($mysqli);

        $this->assertNotNull($reward);

        $mysqli->rollback();
    }

    public function testRewardCronNotToday(): void
    {
        require __DIR__ . '/../config.php';

        $mysqli->begin_transaction();

        $today = date('Y-m-d');
        $stmt = $mysqli->prepare("UPDATE rewards SET date = ? WHERE date = ?");
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        $stmt->bind_param("ss", $tomorrow, $today);
        $stmt->execute();
        $stmt->close();

        $GLOBALS['__TEST_INPUT__'] = true;

        ob_start();
        require __DIR__ . '/../cron/reward_cron.php';
        $output = ob_get_clean();

        $this->assertEquals('NOT_TODAY', trim($output));

        unset($GLOBALS['__TEST_INPUT__']);
        $mysqli->rollback();
    }

    public function testRewardCronToday(): void
    {
        require __DIR__ . '/../config.php';

        $mysqli->begin_transaction();

        $today = date('Y-m-d');
        $result = $mysqli->query("SELECT id FROM rewards WHERE reward_sent = 0 ORDER BY date ASC LIMIT 1");
        $row = $result->fetch_assoc();
        $rewardId = $row['id'];

        $stmt = $mysqli->prepare("UPDATE rewards SET date = ? WHERE id = ?");
        $stmt->bind_param("ss", $today, $rewardId);
        $stmt->execute();
        $stmt->close();

        $GLOBALS['__TEST_INPUT__'] = true;

        ob_start();
        require __DIR__ . '/../cron/reward_cron.php';
        $output = ob_get_clean();

        $this->assertEquals('REWARD_SENT', trim($output));

        unset($GLOBALS['__TEST_INPUT__']);
        $mysqli->rollback();
    }
}
