<?php

namespace GenerateDifferences\GetDiff;
use function GenerateDifferences\Parser\parse;
use function GenerateDifferences\Parser\findDiffs;

function getDiff($pathToFile1, $pathToFile2, $format = "json")
{
    if (file_exists($pathToFile1) && file_exists($pathToFile2)) {
        $stringFile1 = file_get_contents($pathToFile1);
        $stringFile2 = file_get_contents($pathToFile2);
    }

    $parsedContent1 = parse($stringFile1);
    $parsedContent2 = parse($stringFile2);

    $result = findDiffs($parsedContent1, $parsedContent2);
    if ($format == "json") {
        $string = json_encode($result, JSON_PRETTY_PRINT) . "\n";
    }
    $stringResult = implode("", explode('"', $string));

    return implode(" ", explode('  ', $stringResult));
}
