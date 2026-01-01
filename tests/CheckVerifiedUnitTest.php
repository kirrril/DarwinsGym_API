<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class CheckVerifiedUnitTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        require_once __DIR__ . '/../check_verified_functions.php';
    }

    public function testValidateUsername(): void
    {
        $this->assertFalse(validateUsername(null));
        $this->assertFalse(validateUsername(''));
        $this->assertTrue(validateUsername('Pissarro'));
    }

    public function testGetEmailVerificationStatus(): void
    {
        require __DIR__ . '/../config.php';

        $this->assertTrue(getEmailVerificationStatus($mysqli, 'Monet'));
        $this->assertFalse(getEmailVerificationStatus($mysqli, 'Manet'));
        $this->assertNull(getEmailVerificationStatus($mysqli, 'Sisley'));
    }

    public function testStatusToJson(): void
    {
        $this->assertEquals("verified", statusToJson(true)['status']);
        $this->assertEquals("pending", statusToJson(false)['status']);
        $this->assertEquals("error", statusToJson(null)['status']);
    }
}
