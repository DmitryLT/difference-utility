<?php

namespace Differ\Cli;
use function Differ\Parser\getDiff;
use Docopt;

const OPTIONS = <<<DOC
Generate diff

Usage:
    gendiff (-h|--help)
    gendiff [--format <fmt>] <firstFile> <secondFile>

Options:
    -h --help           Show this screen
    --format <fmt>      Report format [default: pretty]

DOC;

function run()
{
    $response = Docopt::handle(OPTIONS);

    $fileBefore = $response->args["<firstFile>"];
    $fileAfter = $response->args["<secondFile>"];

    echo getDiff($fileBefore, $fileAfter);
}
