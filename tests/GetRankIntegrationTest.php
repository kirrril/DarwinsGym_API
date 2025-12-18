<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class GetRankIntegrationTest extends TestCase
{
    public function testUsernameAndTokenMissingReturnsError(): void
    {
        $_GET = [];
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
        $_GET = ['player_name' => 'Balzac'];
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
        $_GET = ['token' => 'balzac123'];
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
        $_GET = ['player_name' => 'Balzac', 'token' => 'balzac123'];
        require __DIR__ . '/../config.php';
        ob_start();
        require __DIR__ . '/../get_rank.php';
        $output = json_decode(ob_get_clean());

        $this->assertEquals(
            "success",
            $output->status
        );

        $this->assertEquals(
            "3",
            $output->rank
        );
    }

    public function testFalseTokenReturnsError(): void
    {
        $_GET = ['player_name' => 'Balzac', 'token' => 'balzac321'];
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
        $_GET = ['player_name' => 'Hugo', 'token' => 'hugo123'];
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
