<?php

namespace Differ\Parser;
use Symfony\Component\Yaml\Yaml;
use function Funct\Collection\flattenAll;

const LAST_COMMA = -2;

function parse($data)
{
    if (pathinfo($data, PATHINFO_EXTENSION) == "json") {
        return json_decode(file_get_contents($data), true);
    } elseif (pathinfo($data, PATHINFO_EXTENSION) == "yml") {
        return Yaml::parse(file_get_contents($data));
    }
}

function findDiffs($arr1, $arr2)
{
    $diffs = array_reduce($arr1, function ($acc, $item) use ($arr1, $arr2) {
        foreach ($arr1 as $key => $value) {
            if (in_array($key, array_keys($arr2)) && in_array($key, array_keys($arr1))) {
                if ($arr1[$key] == $arr2[$key]) {
                    $acc["  {$key}"] = $value;
                } elseif ($arr1[$key] != $arr2[$key]) {
                    $acc["- {$key}"] = $value;
                    $acc["+ {$key}"] = $arr2[$key];
                }
            }
        }
        foreach ($arr1 as $key => $value) {
            if (!in_array($key, array_keys($arr2))) {
                $acc["- {$key}"] = $value;
            }
        }
        foreach ($arr2 as $key => $value) {
            if (!in_array($key, array_keys($arr1))) {
                $acc["+ {$key}"] = $value;
            }
        }
        return $acc;
    }, []);
    $keys = array_keys($diffs);
    $values = flattenAll($diffs);
    $result = [];
    for ($i = 0; $i < count($keys); $i++) {
        $result[$keys[$i]] = $values[$i];
    }
    return $result;
}

function stringifyResult($result)
{
    $string = "{\n";
    foreach ($result as $key => $value) {
        if (is_bool($value)) {
            $value = $value == true ? 'true' : 'false';
        }
        $string = "{$string}  {$key}: {$value},\n";
    }
    $noLastComma = substr($string, 0, LAST_COMMA);
    $finalString = "{$noLastComma}\n}\n";
    return $finalString;
}
