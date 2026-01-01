<?php
declare(strict_types=1);
use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        require_once __DIR__ . '/../login_functions.php';
    }

    public function testvalidatePassword(): void
    {
        require __DIR__ . '/../config.php';

        $this->assertEquals(true, validatePassword($mysqli, 'Monet', 'Password1'));
        $this->assertEquals(false, validatePassword($mysqli, 'Pissarro', 'Password1'));
    }

    public function testvalidateEmail(): void
    {
        require __DIR__ . '/../config.php';

        $this->assertTrue(validateEmail($mysqli, 'Monet'));
        $this->assertFalse(validateEmail($mysqli, 'Manet'));
    }

    public function testRightCredentialsReturnSuccess(): void
    {
        $GLOBALS['__TEST_INPUT__'] = json_encode([
            'player_name' => 'Monet',
            'password' => 'Password1'
        ]);
        require __DIR__ . '/../config.php';
        ob_start();
        require __DIR__ . '/../login.php';
        $output = json_decode(ob_get_clean());

        unset($GLOBALS['__TEST_INPUT__']);

        $this->assertEquals("success", $output->status);
    }

    public function testWrongPasswordReturnsInvalid(): void
    {
        $GLOBALS['__TEST_INPUT__'] = json_encode([
            'player_name' => 'Pissarro',
            'password' => 'Password1'
        ]);
        require __DIR__ . '/../config.php';
        ob_start();
        require __DIR__ . '/../login.php';
        $output = json_decode(ob_get_clean());

        unset($GLOBALS['__TEST_INPUT__']);

        $this->assertEquals("Invalid credentials", $output->message);
    }

    public function testEmailNotVerified(): void
    {
        $GLOBALS['__TEST_INPUT__'] = json_encode([
            'player_name' => 'Manet',
            'password' => 'Password'
        ]);
        require __DIR__ . '/../config.php';
        ob_start();
        require __DIR__ . '/../login.php';
        $output = json_decode(ob_get_clean());

        unset($GLOBALS['__TEST_INPUT__']);

        $this->assertEquals("Email not verified", $output->message);
    }
}
