<?php

namespace Differ\Differ;

use Gendiff\Parsers;
use Gendiff\Formatters;

use function Functional\sort;

function gendiff(string $pathToFile1, string $pathToFile2, string $format = 'stylish')
{
    $inputArr1 = Parsers\parseToArray($pathToFile1);
    $inputArr2 = Parsers\parseToArray($pathToFile2);
    $diff = buildDiff($inputArr1, $inputArr2);
    return Formatters\format($diff, $format);
}

function buildDiff(array $array1, array $array2)
{
    $preResultKeys = array_map(function ($key, $value) use ($array1, $array2) {
        if (array_key_exists($key, $array2)) {
            if ((isAssoc($array1[$key])) && (isAssoc($array2[$key]))) {
                return $key . '0 ';
            } elseif (isAssoc($array1[$key])) {
                return $key . "1-\n" . $key . '2+';
            } elseif (isAssoc($array2[$key])) {
                return $key . "1-\n" . $key . '2+';
            } elseif (($value === $array2[$key]) or (is_array($value))) {
                return $key . '0 ';
            } else {
                return $key . "1-\n" . $key . '2+';
            }
        } else {
            return $key . '0-';
        }
    }, array_keys($array1), $array1);
    $preResultValues = array_map(function ($key, $value) use ($array1, $array2) {
        if (array_key_exists($key, $array2)) {
            if ((isAssoc($array1[$key])) && (isAssoc($array2[$key]))) {
                return buildDiff($array1[$key], $array2[$key]);
            } elseif (isAssoc($array1[$key])) {
                return [['+value+' => getChildren($array1[$key]), '+removeThisLine+' => 'yes'],
                        ['+value+' => normValue($array2[$key]), '+other value+' => getChildren($array1[$key])]
                       ];
            } elseif (isAssoc($array2[$key])) {
                return [['+value+' => normValue($array1[$key]), '+removeThisLine+' => 'yes'],
                        ['+value+' => getChildren($array2[$key]), '+other value+' => normValue($array1[$key])]
                       ];
            } elseif (($value === $array2[$key]) or (is_array($value))) {
                  return normValue($value);
            } else {
                return [['+value+' => normValue($value), '+removeThisLine+' => 'yes'],
                        ['+value+' => normValue($array2[$key]), '+other value+' => normValue($value)]
                       ];
            }
        } else {
            if (isAssoc($array1[$key])) {
                return getChildren($array1[$key]);
            } else {
                  return normValue($value);
            }
        }
    }, array_keys($array1), $array1);
    $preResultPart1 = array_combine(mendArray($preResultKeys), mendArray($preResultValues));
    $preResultPart2Keys = array_map(function ($key) use ($array1) {
        if (!array_key_exists($key, $array1)) {
                return $key . '0+';
        }
    }, array_keys($array2));
    $preResultPart2Values = array_map(function ($key, $value) use ($array1, $array2) {
        if (!array_key_exists($key, $array1)) {
            if (isAssoc($array2[$key])) {
                return getChildren($array2[$key]);
            } else {
                  return normValue($value);
            }
        }
    }, array_keys($array2), $array2);
    $preResultPart2 = array_filter(array_combine(mendArray($preResultPart2Keys), mendArray($preResultPart2Values)), fn($value) => !is_null($value)); // phpcs:ignore
    $preResultNew = array_merge($preResultPart1, $preResultPart2);
    $preResultNewKeys = array_keys($preResultNew);
    $preResultNewKeysSorted = sort($preResultNewKeys, fn ($left, $right) => strcmp($left, $right));
    $preResultNewSorted = array_merge(array_flip($preResultNewKeysSorted), $preResultNew);
    $resultNewKeys = array_map(function ($key, $value) {
        $prefix = $key[-1];
        $keyNew = $prefix . " " . substr($key, 0, -2);
        return $keyNew;
    }, array_keys($preResultNewSorted), $preResultNewSorted);
    $newResult = array_combine($resultNewKeys, $preResultNewSorted);
    return $newResult;
}

function normValue(mixed $input)
{
    $value = ((is_bool($input)) or ($input === null)) ? var_export($input, true) : $input;
    if ($value === "NULL") {
        return "null";
    }
    return $value;
}

function isAssoc(mixed $arr)         // взял со Stackoverflow, чуток подправил
{
    if ((!is_array($arr)) or (array() === $arr)) {
        return false;
    }
    return array_keys($arr) !== range(0, count($arr) - 1);
}

function getChildren(array $array)         // весь смысл данной ф-ии - корректная индентация вложенных ассоц.массивов
{
    $newKeys = array_map(function ($k) {
            return '  ' . $k;
    }, array_keys($array));
    $newValues = array_map(function ($v) {
        return (isAssoc($v)) ? getChildren($v) : $v;
    }, $array);
    return array_combine($newKeys, $newValues);
}

function mendArray(array $input)
{
    $arrMended = array_reduce($input, function ($acc, $item) {
        if (!is_array($item)) {
            return array_merge($acc, (strstr($item, PHP_EOL) !== false) ? explode("\n", $item) : [$item]);
        } else {
            if ((array_key_exists(0, $item)) and ((count($item) === 2) and isAssoc($item[0]) and (isAssoc($item[1])))) {  // phpcs:ignore
                return array_merge($acc, [$item[0]], [$item[1]]);
            } else {
                return array_merge($acc, [$item]);
            }
        }
    }, []);
    return $arrMended;
}
