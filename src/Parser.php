<?php

namespace Differ\Parser;
use Symfony\Component\Yaml\Yaml;

const LAST_COMMA = -2;
const FIRST_INDENTATION = 0;

function parse($data, $extension)
{
    if ($extension == "json") {
        return json_decode($data, true);
    } elseif ($extension == "yml") {
        $data = Yaml::parse($data);
        return standartizeYamlToJson($data);
    }
}

function getExtension($pathToFile)
{
    return pathinfo($pathToFile, PATHINFO_EXTENSION);
}

function findDiffs($before, $after)
{
    $differences = [];
    foreach ($before as $key => $value) {
        if (array_key_exists($key, $after) && $before[$key] == $after[$key]) {
            array_push($differences, ['key' => $key, 'value' => $before[$key], 'change' => ' ']);
        } elseif (array_key_exists($key, $after) && $before[$key] != $after[$key]) {
            array_push($differences, ['key' => $key, 'value' => $before[$key], 'change' => '-']);
            array_push($differences, ['key' => $key, 'value' => $after[$key], 'change' => '+']);
        } elseif (!array_key_exists($key, $after)) {
            array_push($differences, ['key' => $key, 'value' => $before[$key], 'change' => '-']);
        }
    }
    foreach ($after as $key => $value) {
        if (!array_key_exists($key, $before)) {
            array_push($differences, ['key' => $key, 'value' => $after[$key], 'change' => '+']);
        }
    }
    return $differences;
}

function standartizeYamlToJson($data)
{
    foreach ($data as $key => $value) {
        $data[$key] = $value[FIRST_INDENTATION];
    }
    return $data;
}

function stringifyResult($result)
{
    $string = "{\n";
    foreach ($result as $key => $value) {
        if (is_bool($value['value'])) {
            $value['value'] = $value['value'] == true ? 'true' : 'false';
        }
        $string = $string . "  " . $value['change'] . " " . $value['key'] . ": " . $value['value'] . ",\n";
    }
    $noLastComma = substr($string, 0, LAST_COMMA);
    $finalString = "{$noLastComma}\n}\n";
    return $finalString;
}
