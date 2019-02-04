<?php

// use function Generate\Differences\GetDiff\getDiff;

require_once "/../getDiff.php";

class GetDiffTest extends \PHPUnit_Framework_TestCase
{
    public function testgetDiff()
    {
        $expected = "{
            host: hexlet.io
          + timeout: 20
          - timeout: 50
          - proxy: 123.234.53.22
          + verbose: true
        }";
        $actual = getDiff();
        $this->assertEquals($expected, $actual);
    }
}
