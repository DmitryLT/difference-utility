<?php

namespace Differ\Parser;
use Symfony\Component\Yaml\Yaml;
use function \Funct\Collection\union;

const LAST_COMMA = -2;
const FIRST_INDENTATION = 0;

function parse($data, $type)
{
    if ($type == "json") {
        return json_decode($data, true);
    } elseif ($type == "yml") {
        return standartizeYamlToJson(Yaml::parse($data));
    }
}

function getType($pathToFile)
{
    return pathinfo($pathToFile, PATHINFO_EXTENSION);
}

function findDiffs($before, $after)
{
    $statuses = findFlags($before, $after);
    $reduced = array_reduce($statuses, function ($acc, $status) use ($before, $after) {
        if (array_key_exists($status['key'], $before)) {
            if (is_bool($before[$status['key']])) {
                $before[$status['key']] = $before[$status['key']] == true ? 'true' : 'false';
            }
        }
        if (array_key_exists($status['key'], $after)) {
            if (is_bool($after[$status['key']])) {
                $after[$status['key']] = $after[$status['key']] == true ? 'true' : 'false';
            }
        }
        if ($status['flag'] == "same") {
            $string = "  " . "  " . $status['key'] . ": " . $before[$status['key']] . "\n";
        } elseif ($status['flag'] == "change") {
            $string = "  " . "- " . $status['key'] . ": " . $before[$status['key']] . "\n" .
                      "  " . "+ " . $status['key'] . ": " . $after[$status['key']] . "\n";
        } elseif ($status['flag'] == "deleted") {
            $string = "  " . "- " . $status['key'] . ": " . $before[$status['key']] . "\n";
        } elseif ($status['flag'] == "added") {
            $string = "  " . "+ " . $status['key'] . ": " . $after[$status['key']] . "\n";
        }
            return $acc . $string;
    }, "");
    return "{\n" . $reduced . "}\n";
}

function findFlags($before, $after)
{
    $keys = union(array_keys($before), array_keys($after));
    $statuses = array_map(function ($key) use ($before, $after) {
        if (array_key_exists($key, $before) && array_key_exists($key, $after)) {
            if ($before[$key] == $after[$key]) {
                $status = "same";
            } elseif ($before[$key] != $after[$key]) {
                $status = "change";
            }
        } elseif (!array_key_exists($key, $after)) {
            $status = "deleted";
        } elseif (!array_key_exists($key, $before)) {
            $status = "added";
        }
        return ['key' => $key, 'flag' => $status];
    }, $keys);
    return $statuses;
}

function standartizeYamlToJson($data)
{
    return $mapped = array_map(function ($value) {
        return $value[FIRST_INDENTATION];
    }, $data);
}
