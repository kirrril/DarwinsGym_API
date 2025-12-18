<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class GetRankUnitTests extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        require_once __DIR__ . '/../get_rank_functions.php';
    }

    public function testValdateNameAndToken()
    {
        $this->assertFalse(validateNameAndToken(null, null));
        $this->assertFalse(validateNameAndToken("name", null));
        $this->assertFalse(validateNameAndToken(null, "token"));
        $this->assertFalse(validateNameAndToken("", "dqsf4g6df5g"));
        $this->assertTrue(validateNameAndToken("name", "dqsf4g6df5g"));
    }

    public function testGetPlayerId()
    {
        $mysqli = require __DIR__ . '/../config.php';
        $this->assertEquals(1, getPlayerId($mysqli, "Balzac"));
        $this->assertNotEquals(20, getPlayerId($mysqli, "Hugo"));
        $this->assertEquals(null, getPlayerId($mysqli, "Maupassant"));
        $this->assertEquals(null, getPlayerId($mysqli, null));
    }

    public function testCheckTokenGetId()
    {
        $mysqli = require __DIR__ . '/../config.php';
        $this->assertEquals(1, checkTokenGetId($mysqli, "balzac123"));
        $this->assertEquals(null, getPlayerId($mysqli, "balzac321"));
        $this->assertNotEquals(20, getPlayerId($mysqli, "hugo123"));
        $this->assertEquals(null, getPlayerId($mysqli, "hugo123"));
        $this->assertEquals(null, getPlayerId($mysqli, "maupassant123"));
        $this->assertEquals(null, checkTokenGetId($mysqli, null));
        $this->assertEquals(2, checkTokenGetId($mysqli, "flaubert123"));
    }

    public function testCheckPlayer()
    {
        $this->assertFalse(checkPlayer(22, "balzac123"));
        $this->assertNull(checkPlayer(22, null));
        $this->assertFalse(checkPlayer(123, "123"));
        $this->assertTrue(checkPlayer(1, 1));
        $this->assertTrue(checkPlayer(6548412116546848, 6548412116546848));
    }

    public function testGetPlayerScore()
    {
        $mysqli = require __DIR__ . '/../config.php';
        $this->assertEquals(10, getPlayerScore($mysqli, 1));
        $this->assertNotEquals(10, getPlayerScore($mysqli, 2));
        $this->assertNull(getPlayerScore($mysqli, null));
        $this->assertNull(getPlayerScore($mysqli, 4));
        $this->assertEquals(30, getPlayerScore($mysqli, 3));
    }

    public function testGetPlayerRank()
    {
        $mysqli = require __DIR__ . '/../config.php';
        $this->assertNull(getPlayerRank($mysqli, null));
        $this->assertEquals(4, getPlayerRank($mysqli, 1));
        $this->assertEquals(1, getPlayerRank($mysqli, 99));
    }
}
