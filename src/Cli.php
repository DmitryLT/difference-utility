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

    $firstFile = $response->args["<firstFile>"];
    $secondFile = $response->args["<secondFile>"];

    echo getDiff($firstFile, $secondFile);
    // getDiff($firstFile, $secondFile);
}
