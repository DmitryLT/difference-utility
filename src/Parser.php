<?php

namespace GenerateDifferences\Parser;
use Symphony\Component\Yaml;

function parse($data)
{
    if (pathinfo($data, PATHINFO_EXTENSION) == "json") {
        return json_decode(file_get_contents($data), true);
    } elseif (pathinfo($data, PATHINFO_EXTENSION) == "yaml") {
        return Yaml::parse(file_get_contents($data));
    }
}

function findDiffs($arr1, $arr2)
{
    $result = [];

    foreach ($arr1 as $key => $value) {
        if (in_array($key, array_keys($arr2)) && in_array($key, array_keys($arr1))) {
            if ($arr1[$key] == $arr2[$key]) {
                $result["   {$key}"] = $value;
            } elseif ($arr1[$key] != $arr2[$key]) {
                $result["- {$key}"] = $value;
                $result["+ {$key}"] = $arr2[$key];
            }
        }
    }
    foreach ($arr1 as $key => $value) {
        if (!in_array($key, array_keys($arr2))) {
            $result["- {$key}"] = $value;
        }
    }
    foreach ($arr2 as $key => $value) {
        if (!in_array($key, array_keys($arr1))) {
            $result["+ {$key}"] = $value;
        }
    }
    return $result;
}
