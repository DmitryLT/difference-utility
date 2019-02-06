<?php

namespace Differ\Parser;
use function Differ\Parser\parse;
use function Differ\Parser\findDiffs;
use function Differ\Parser\stringifyResult;

function getDiff($pathToFile1, $pathToFile2)
{
    $parsedContent1 = parse($pathToFile1);
    $parsedContent2 = parse($pathToFile2);

    $result = findDiffs($parsedContent1, $parsedContent2);

    return stringifyResult($result);
}
