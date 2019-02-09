<?php

namespace Differ\Parser;
use Symfony\Component\Yaml\Yaml;
use function \Funct\Collection\union;
use function \Funct\Collection\flatten;

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
    $statuses = findNodeTypes($before, $after);
    $mapped = array_map(function ($status) use ($before, $after) {
        [$key, $valueBefore, $valueAfter, $type] = $status;
        if ($type == "same") {
            return "    {$key}: {$valueBefore}";
        } elseif ($type == "change") {
            return ["  - {$key}: {$valueBefore}", "  + {$key}: {$valueAfter}"];
        } elseif ($type == "deleted") {
            return "  - {$key}: {$valueBefore}";
        } elseif ($type == "added") {
            return "  + {$key}: {$valueAfter}";
        }
    }, $statuses);
    $fullString = implode("\n", flatten($mapped));
    return "{\n{$fullString}\n}\n";
}

function stringify($value)
{
    if (is_bool($value)) {
        $value = $value == true ? 'true' : 'false';
        return $value;
    }
    return $value;
}

function findNodeTypes($before, $after)
{
    $keys = union(array_keys($before), array_keys($after));
    $statuses = array_map(function ($key) use ($before, $after) {
        $beforeValue = isset($before[$key]) ? stringify($before[$key]) : '';
        $afterValue = isset($after[$key]) ? stringify($after[$key]) : '';
        if (array_key_exists($key, $before) && array_key_exists($key, $after)) {
            if ($before[$key] == $after[$key]) {
                $type = "same";
            } elseif ($before[$key] != $after[$key]) {
                $type = "change";
            }
        } elseif (!array_key_exists($key, $after)) {
            $type = "deleted";
        } elseif (!array_key_exists($key, $before)) {
            $type = "added";
        }
        return [$key, $beforeValue, $afterValue, $type];
    }, $keys);
    return $statuses;
}

function standartizeYamlToJson($data)
{
    return $mapped = array_map(function ($value) {
        return $value[FIRST_INDENTATION];
    }, $data);
}
