<?php

namespace Differ\Cli;

use function Differ\Differ\getDiff;
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
    $response = Docopt::handle(OPTIONS); // using OPTIONS and docopt library we give the information how to interprete commands from cli

    $fileBefore = $response->args["<firstFile>"]; // get the path to 1 file from cli to the variable as a string
    $fileAfter = $response->args["<secondFile>"]; // get the path to 2 file from cli to the variable as a string

    echo getDiff($fileBefore, $fileAfter);
}
