<?php
declare(strict_types=1);
use PHPUnit\Framework\TestCase;

class CheckVerifiedIntegrationTest extends TestCase
{
    public function testUsernameMissingReturnsError(): void
    {
        $_GET = [];
        require __DIR__ . '/../config.php';
        ob_start();
        require __DIR__ . '/../check_verified.php';
        $output = json_decode(ob_get_clean());

        $this->assertEquals("error", $output->status);
    }

    public function testEmailVerifiedReturnsVerified(): void
    {
        $_GET = ['username' => 'Cezanne'];
        require __DIR__ . '/../config.php';
        ob_start();
        require __DIR__ . '/../check_verified.php';
        $output = json_decode(ob_get_clean());

        $this->assertEquals("verified", $output->status);
    }

    public function testEmailPendingReturnsPending(): void
    {
        $_GET = ['username' => 'Manet'];
        require __DIR__ . '/../config.php';
        ob_start();
        require __DIR__ . '/../check_verified.php';
        $output = json_decode(ob_get_clean());

        $this->assertEquals("pending", $output->status);
    }

    public function testUsernameUnknownReturnsError(): void
    {
        $_GET = ['username' => 'Sisley'];
        require __DIR__ . '/../config.php';
        ob_start();
        require __DIR__ . '/../check_verified.php';
        $output = json_decode(ob_get_clean());

        $this->assertEquals("error", $output->status);
    }
}
