<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class GetRankUnitTest extends TestCase
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

    public function testGetScoreData()
    {
        $mysqli = require __DIR__ . '/../config.php';
        $this->assertEquals(null, getScoreData($mysqli, "Pissaro", null));
        $this->assertEquals(null, getScoreData($mysqli, null, "9e79d1337348d2343e3763b6bdd506bc61bb0ef7787c9d99d1880b75ba675329"));
        $this->assertEquals(['score' => '141', 'score_updated_at' => '2025-11-12 09:11:48'], getScoreData($mysqli, "Pissarro", "9e79d1337348d2343e3763b6bdd506bc61bb0ef7787c9d99d1880b75ba675329"));
    }

    public function testGetRank()
    {
        $mysqli = require __DIR__ . '/../config.php';
        $this->assertEquals(1, getRank($mysqli, ['score' => '141', 'score_updated_at' => '2025-11-12 09:11:48']));
        $this->assertEquals(3, getRank($mysqli, ['score' => '85', 'score_updated_at' => '2025-11-01 07:55:03']));
        $this->assertNotEquals(1, getRank($mysqli, ['score' => '85', 'score_updated_at' => '2025-11-01 07:55:03']));
    }

    public function testRankToJson()
    {
        $this->assertEquals(['status' => 'success', 'rank' => 26], rankToJson(26));
        $this->assertEquals(['status' => 'error'], rankToJson(null));
        $this->assertEquals(['status' => 'error'], rankToJson(-2));
    }
}
