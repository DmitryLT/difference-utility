<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;
use function \Differ\Differ\getDiff;
use function \Funct\Strings\strip;

class GetDiffTest extends TestCase
{
    public function testgetDiff()
    {
        $expected1 = file_get_contents(__DIR__ . "/examples/expectedJson.txt");
        $expected2 = file_get_contents(__DIR__ . "/examples/expectedNested.txt");
        $actual1 = trim(getDiff(__DIR__ . "/examples/before.json", __DIR__ . "/examples/after.json"));
        $actual2 = trim(getDiff(__DIR__ . "/examples/before.yml", __DIR__ . "/examples/after.yml"));
        $actual3 = trim(getDiff(__DIR__ . "/examples/beforeNested.json", __DIR__ . "/examples/afterNested.json"));

        $this->assertEquals($expected1, $actual1);
        $this->assertEquals($expected1, $actual2);
        $this->assertEquals($expected2, $actual3);
    }
}
