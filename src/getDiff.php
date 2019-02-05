<?php

namespace GenerateDifferences\GetDiff;
use function GenerateDifferences\Parser\parse;
use function GenerateDifferences\Parser\findDiffs;
use function GenerateDifferences\Parser\stringifyResult;

function getDiff($pathToFile1, $pathToFile2)
{
    $parsedContent1 = parse($pathToFile1);
    $parsedContent2 = parse($pathToFile2);

    $result = findDiffs($parsedContent1, $parsedContent2);

    return stringifyResult($result);
}
