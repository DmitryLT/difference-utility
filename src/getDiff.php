<?php

namespace Differ\Differ;

use function Differ\Parser\parse;
use function Differ\Parser\findDiffs;
use function Differ\Parser\findNodes;
use function Differ\Parser\recurStrings;
use function Differ\Parser\stringifyResult;
use function Differ\Parser\getType;

function getDiff($fileBefore, $fileAfter)
{
    $parsedContent1 = parse(file_get_contents($fileBefore), getType($fileBefore));
    $parsedContent2 = parse(file_get_contents($fileAfter), getType($fileAfter));

    $nodes = findNodes($parsedContent1, $parsedContent2);

    $result = recurStrings($nodes);

    return $result;
}
