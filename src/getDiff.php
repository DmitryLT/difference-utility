<?php

namespace Differ\Parser;
use function Differ\Parser\parse;
use function Differ\Parser\findDiffs;
use function Differ\Parser\stringifyResult;
use function Differ\Parser\getExtension;

function getDiff($fileBefore, $fileAfter)
{
    $parsedContent1 = parse(file_get_contents($fileBefore), getExtension($fileBefore));
    $parsedContent2 = parse(file_get_contents($fileAfter), getExtension($fileAfter));

    $result = findDiffs($parsedContent1, $parsedContent2);

    return stringifyResult($result);
}
