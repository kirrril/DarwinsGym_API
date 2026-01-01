<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class UpdateScoreTest extends TestCase
{
    public function testHigherScoreIsUpdated(): void
    {
        require __DIR__ . '/../config.php';

        $mysqli->query("UPDATE players SET score = 10 WHERE player_name = 'Monet'");

        $GLOBALS['__TEST_INPUT__'] = json_encode([
            'player_name' => 'Monet',
            'token' => '6a5c7e3b4c086fd43e5109a7962ffddf3a9a01cfb889f9a4084778f8a1ba0771',
            'score' => 19
        ]);

        ob_start();
        require __DIR__ . '/../update_score.php';
        $output = json_decode(ob_get_clean());

        unset($GLOBALS['__TEST_INPUT__']);

        $this->assertEquals("success", $output->status);
        $this->assertEquals("Score updated", $output->message);
    }

    public function testLowerScoreIsNotUpdated(): void
    {
        require __DIR__ . '/../config.php';

        $mysqli->query("UPDATE players SET score = 120 WHERE player_name = 'Cezanne'");

        $GLOBALS['__TEST_INPUT__'] = json_encode([
            'player_name' => 'Cezanne',
            'token' => '7082d2290ec59fa0cb50f7088819af45e0b92a006837a51a44d94a0a8b40a48d',
            'score' => 100
        ]);

        ob_start();
        require __DIR__ . '/../update_score.php';
        $output = json_decode(ob_get_clean());

        unset($GLOBALS['__TEST_INPUT__']);

        $this->assertEquals("success", $output->status);
        $this->assertEquals("Score not improved", $output->message);
    }
}
