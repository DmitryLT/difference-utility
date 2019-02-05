<?php

namespace GenerateDifferences\GetDiff;
use function \Funct\Strings\strip;
use Docopt;

function getDiff($pathToFile1, $pathToFile2)
{
    if (file_exists($pathToFile1) && file_exists($pathToFile2)) {
        $stringFile1 = file_get_contents($pathToFile1);
        $stringFile2 = file_get_contents($pathToFile2);
    }

    $arr1 = json_decode($stringFile1, true);
    $arr2 = json_decode($stringFile2, true);

    $result = [];

    foreach ($arr1 as $key => $value) {
        if (in_array($key, array_keys($arr2)) && in_array($key, array_keys($arr1))) {
            if ($arr1[$key] == $arr2[$key] && in_array($key, array_keys($arr2)) && in_array($key, array_keys($arr1))) {
                $result["  $key"] = $value;
            } elseif ($arr1[$key] != $arr2[$key] && !empty($arr1[$key])) {
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

    $string = json_encode($result, JSON_PRETTY_PRINT) . "\n";
    $stringResult = \Funct\Strings\strip($string, '"');

    return $stringResult;
}
