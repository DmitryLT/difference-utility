<?php

namespace GenerateDifferences\Tests;
use PHPUnit\Framework\TestCase;
use function \GenerateDifferences\GetDiff\getDiff;
use function \Funct\Strings\strip;

class GetDiffTest extends TestCase
{
    public function testgetDiff()
    {
        $expected = file_get_contents(__DIR__ . "/examples/expectedJson.txt");
        $actual1 = trim(getDiff(__DIR__ . "/examples/before.json", __DIR__ . "/examples/after.json"));
        $actual2 = trim(getDiff(__DIR__ . "/examples/before.yml", __DIR__ . "/examples/after.yml"));

        $this->assertEquals($expected, $actual1);
        $this->assertEquals($expected, $actual2);
    }
}
