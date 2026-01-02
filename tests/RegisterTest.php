<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class RegisterTest extends TestCase
{
    public function testInvalidUsername(): void
    {
        $GLOBALS['__TEST_INPUT__'] = json_encode([
            'player_name' => 'De',
            'email' => 'edgar@degas.fr',
            'password' => 'Password6'
        ]);

        ob_start();
        require __DIR__ . '/../register.php';
        $output = json_decode(ob_get_clean());

        unset($GLOBALS['__TEST_INPUT__']);

        $this->assertEquals("error", $output->status);
        $this->assertEquals("Invalid username", $output->message);
    }

    public function testInvalidEmail(): void
    {
        $GLOBALS['__TEST_INPUT__'] = json_encode([
            'player_name' => 'Degas',
            'email' => 'edgar@degas',
            'password' => 'Password6'
        ]);

        ob_start();
        require __DIR__ . '/../register.php';
        $output = json_decode(ob_get_clean());

        unset($GLOBALS['__TEST_INPUT__']);

        $this->assertEquals("error", $output->status);
        $this->assertEquals("Invalid email", $output->message);
    }

    public function testInvalidPassword(): void
    {
        $GLOBALS['__TEST_INPUT__'] = json_encode([
            'player_name' => 'Degas',
            'email' => 'edgar@degas.fr',
            'password' => 'Pass6'
        ]);

        ob_start();
        require __DIR__ . '/../register.php';
        $output = json_decode(ob_get_clean());

        unset($GLOBALS['__TEST_INPUT__']);

        $this->assertEquals("error", $output->status);
        $this->assertEquals("Password too short", $output->message);
    }

    public function testPlayerNameAlreadyTaken(): void
    {
        require __DIR__ . '/../config.php';

        $mysqli->begin_transaction();

        $GLOBALS['__TEST_INPUT__'] = json_encode([
            'player_name' => 'Monet',
            'email' => 'edgar@degas.fr',
            'password' => 'Password6'
        ]);

        ob_start();
        require __DIR__ . '/../register.php';
        $output = json_decode(ob_get_clean());

        unset($GLOBALS['__TEST_INPUT__']);

        $this->assertEquals("error", $output->status);
        $this->assertEquals("Username or email already taken", $output->message);

        $mysqli->rollback();
    }

    public function testEmailAlreadyTaken(): void
    {
        require __DIR__ . '/../config.php';

        $mysqli->begin_transaction();

        $GLOBALS['__TEST_INPUT__'] = json_encode([
            'player_name' => 'Degas',
            'email' => 'claude@monet.fr',
            'password' => 'Password6'
        ]);

        ob_start();
        require __DIR__ . '/../register.php';
        $output = json_decode(ob_get_clean());

        unset($GLOBALS['__TEST_INPUT__']);

        $this->assertEquals("error", $output->status);
        $this->assertEquals("Username or email already taken", $output->message);

        $mysqli->rollback();
    }

    public function testRegisteredAndEmailSent(): void
    {
        require __DIR__ . '/../config.php';
        require __DIR__ . '/../vendor/autoload.php';

        $mysqli->query("DELETE FROM players WHERE email = 'kirillukolov22@gmail.com' OR player_name = 'Ukolov'");

        $GLOBALS['__TEST_INPUT__'] = json_encode([
            'player_name' => 'Ukolov',
            'email' => 'kirillukolov22@gmail.com',
            'password' => 'Password11'
        ]);

        ob_start();
        require __DIR__ . '/../register.php';
        $output = json_decode(ob_get_clean());

        unset($GLOBALS['__TEST_INPUT__']);

        $this->assertEquals("success", $output->status);
        $this->assertEquals("Account created! Check your email.", $output->message);

        $mysqli->query("DELETE FROM players WHERE email = 'kirillukolov22@gmail.com' OR player_name = 'Ukolov'");
    }
}
