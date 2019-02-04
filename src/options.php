<?php

namespace Generate\Differences\Options;
use Docopt;

const OPTIONS = <<<DOC
Usage:
    gendiff (-h|--help)
    gendiff [--format <fmt>] <firstFile> <secondFile>

Options:
    -h --help           Show this screen
    --format <fmt>      Report format [default: pretty]

DOC;

function showOptions()
{
    echo "Generate diff" . PHP_EOL;
    echo PHP_EOL;
    $result = Docopt::handle(OPTIONS, array('version' => '1.0.0rc2'));
    foreach ($result as $k => $v) {
        echo $k . ': ' . json_encode($v) . PHP_EOL;
    }
}
