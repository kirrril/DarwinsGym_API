<?php
declare(strict_types=1);
use PHPUnit\Framework\TestCase;

class CheckVerifiedUnitTests extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        require_once __DIR__ . '/../check_verified_functions.php';
    }

    public function testValidateUsername(): void
    {
        // require_once __DIR__ . '/../check_verified_functions.php';

        $this->assertFalse(validateUsername(null));
        $this->assertFalse(validateUsername(''));
        $this->assertTrue(validateUsername('Balzac'));
    }

    public function testStatusToJson(): void
    {
        // require_once __DIR__ . '/../check_verified_functions.php';

        $this->assertEquals(
            "verified",
            statusToJson(true)['status']
        );

        $this->assertEquals(
            "pending",
            statusToJson(false)['status']
        );

        $this->assertEquals(
            "error",
            statusToJson(null)['status']
        );
    }

    public function testGetEmailVerificationStatus(): void
    {
        require __DIR__ . '/../config.php';

        $this->assertTrue(
            getEmailVerificationStatus($mysqli, 'Balzac')
        );

        $this->assertFalse(
            getEmailVerificationStatus($mysqli, 'Hugo')
        );

        $this->assertNull(
            getEmailVerificationStatus($mysqli, 'Dumas')
        );
    }
}
