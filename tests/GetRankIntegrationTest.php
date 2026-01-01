<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class GetRankIntegrationTest extends TestCase
{
    public function testUsernameAndTokenMissingReturnsError(): void
    {
        $GLOBALS['__TEST_INPUT__'] = json_encode([
            'player_name' => '',
            'token' => ''
        ]);
        require __DIR__ . '/../config.php';
        ob_start();
        require __DIR__ . '/../get_rank.php';
        $output = json_decode(ob_get_clean());

        $this->assertEquals(
            "error",
            $output->status
        );
    }

    public function testTokenMissingReturnsError(): void
    {
        $GLOBALS['__TEST_INPUT__'] = json_encode([
            'player_name' => 'Monet',
            'token' => ''
        ]);
        require __DIR__ . '/../config.php';
        ob_start();
        require __DIR__ . '/../get_rank.php';
        $output = json_decode(ob_get_clean());

        $this->assertEquals(
            "error",
            $output->status
        );
    }

    public function testNameMissingReturnsError(): void
    {
        $GLOBALS['__TEST_INPUT__'] = json_encode([
            'player_name' => '',
            'token' => '7082d2290ec59fa0cb50f7088819af45e0b92a006837a51a44d94a0a8b40a48d'
        ]);
        require __DIR__ . '/../config.php';
        ob_start();
        require __DIR__ . '/../get_rank.php';
        $output = json_decode(ob_get_clean());

        $this->assertEquals(
            "error",
            $output->status
        );
    }

    public function testNameAndTokenOKReturnsRank(): void
    {
        $GLOBALS['__TEST_INPUT__'] = json_encode([
            'player_name' => 'Cezanne',
            'token' => '7082d2290ec59fa0cb50f7088819af45e0b92a006837a51a44d94a0a8b40a48d'
        ]);
        require __DIR__ . '/../config.php';
        ob_start();
        require __DIR__ . '/../get_rank.php';
        $output = json_decode(ob_get_clean());

        $this->assertEquals(
            "success",
            $output->status
        );

        $this->assertEquals(
            "2",
            $output->rank
        );
    }

    public function testFalseTokenReturnsError(): void
    {
        $GLOBALS['__TEST_INPUT__'] = json_encode([
            'player_name' => 'Renoir',
            'token' => '7082d2290ec59fa0cb50f7088819af45e0b92a006837a51a44d94a0a8b40a48d'
        ]);
        require __DIR__ . '/../config.php';
        ob_start();
        require __DIR__ . '/../get_rank.php';
        $output = json_decode(ob_get_clean());

        $this->assertEquals(
            "error",
            $output->status
        );
    }

    public function testExpiredSessionReturnsError(): void
    {
        $GLOBALS['__TEST_INPUT__'] = json_encode([
            'player_name' => 'Manet',
            'token' => '553ef68b01013ad24a47824ee8bb5c2ec803448f9b36337e8ed8b8ad78897638'
        ]);
        require __DIR__ . '/../config.php';
        ob_start();
        require __DIR__ . '/../get_rank.php';
        $output = json_decode(ob_get_clean());

        $this->assertEquals(
            "error",
            $output->status
        );
    }
}
