<?php

namespace GenerateDifferences\GetDiff;
use function GenerateDifferences\Parser\parse;
use function GenerateDifferences\Parser\findDiffs;

function getDiff($pathToFile1, $pathToFile2)
{
    $parsedContent1 = parse($pathToFile1);
    $parsedContent2 = parse($pathToFile2);

    $result = findDiffs($parsedContent1, $parsedContent2);
    $string = json_encode($result, JSON_PRETTY_PRINT) . "\n";
    $stringResult = implode("", explode('"', $string));

    return implode(" ", explode('  ', $stringResult));
}
