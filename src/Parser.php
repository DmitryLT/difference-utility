<?php

namespace Differ\Parser;

use Symfony\Component\Yaml\Yaml;
use function \Funct\Collection\union;
use function \Funct\Collection\flatten;
use function \Funct\Collection\flattenAll;

const LAST_COMMA = -2;
const FIRST_INDENTATION = 0;
const FIRST_SPACES = 4;

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

function recurStrings($nodes)
{
    $mapped = array_map(function ($node) {
        return stringifyRec($node, 0);
    }, $nodes);
    $result = implode("\n", flattenAll($mapped));
    return "{\n" . $result . "\n}";
}

function stringifyRec($node, $layer)
{
    ["type" => $type,
    "key" => $key,
    "beforeValue" => $before,
    "afterValue" => $after,
    "childNodes" => $childNodes] = $node;
    $before = stringifyArray($before, $layer);
    $after = stringifyArray($after, $layer);
    switch ($type) {
        case "childNodes":
            return [getBeginning($layer) . "    {$key}: {", array_map(function ($childNode) use ($layer) {
                return stringifyRec($childNode, $layer + 1);
            }, $childNodes), getBeginning($layer) . "    }"];
        case "same":
            return getBeginning($layer) . "    {$key}: {$before}";
        case "change":
            return [getBeginning($layer) . "  - {$key}: {$before}",
                    getBeginning($layer) . "  + {$key}: {$after}"];
        case "deleted":
            return getBeginning($layer) . "  - {$key}: {$before}";
        case "added":
            return getBeginning($layer) . "  + {$key}: {$after}";
    }
}
// get 4 spaces
function getBeginning($layer)
{
    return str_repeat(" ", FIRST_SPACES * $layer);
}

// displays how match spaces needed before the 'key: value' on current layer
function stringifyArray($value, $layer)
{
    if (is_array($value)) {
        $keys = array_keys($value);
        $result = array_map(function ($key) use ($value, $layer) {
            return ["\n" . getBeginning($layer + 1) . "    {$key}: " . stringifyArray($value[$key], $layer)];
        }, $keys);
        return implode("", array_merge(["{"], flattenAll($result), ["\n" . getBeginning($layer) . "    }"]));
    }
    return $value;
}

function stringifyBool($value)
{
    if (is_bool($value)) {
        $result = $value == true ? 'true' : 'false';
        return $result;
    }
    return $value;
}

// build one array with diffs info
function getNode($key, $beforeValue, $afterValue, $type, $childNodes)
{
    return ["key" => $key,
            "beforeValue" => $beforeValue,
            "afterValue" => $afterValue,
            "type" => $type,
            "childNodes" => $childNodes];
}

// build array of arrays with diffs info
function findNodes($before, $after)
{
    $keys = union(array_keys($before), array_keys($after));
    $nodes = array_map(function ($key) use ($before, $after) {
        $beforeValue = isset($before[$key]) ? stringifyBool($before[$key]) : "";
        $afterValue = isset($after[$key]) ? stringifyBool($after[$key]) : "";
        if (array_key_exists($key, $before) && array_key_exists($key, $after)) {
            if (is_array($beforeValue) && is_array($afterValue)) {
                $node = getNode($key, $beforeValue, $afterValue, "childNodes", findNodes($beforeValue, $afterValue));
            } elseif ($beforeValue == $afterValue) {
                $node = getNode($key, $beforeValue, $afterValue, "same", "");
            } elseif ($beforeValue != $afterValue) {
                $node = getNode($key, $beforeValue, $afterValue, "change", "");
            }
        } elseif (array_key_exists($key, $before) && !array_key_exists($key, $after)) {
            $node = getNode($key, $beforeValue, "", "deleted", "");
        } elseif (!array_key_exists($key, $before)) {
            $node = getNode($key, "", $afterValue, "added", "");
        }
        return $node;
    }, $keys);
    return $nodes;
}

function standartizeYamlToJson($data)
{
    $mapped = array_map(function ($value) {
        return $value[FIRST_INDENTATION];
    }, $data);
    return $mapped;
}

// old function for finding diffs in no nested data
function findDiffs($before, $after)
{
    $statuses = findNodeTypes($before, $after);
    $mapped = array_map(function ($status) use ($before, $after) {
        switch ($status["type"]) {
            case "childNodes":
                return ["{$status["key"]}: ", array_map(function ($childNodes) use ($status) {
                    return findDiffs($status["beforeValue"], $status["afterValue"]);
                }, $status["childNodes"])];
            case "same":
                return "    {$status["key"]}: {$status["beforeValue"]}";
            case "change":
                return ["  - {$status["key"]}: {$status["beforeValue"]}",
                        "  + {$status["key"]}: {$status["afterValue"]}"];
            case "deleted":
                return "  - {$status["key"]}: {$status["beforeValue"]}";
            case "added":
                return "  + {$status["key"]}: {$status["afterValue"]}";
        }
    }, $statuses);
    $fullString = implode("\n", flatten($mapped));
    return "{\n{$fullString}\n}\n";
}
