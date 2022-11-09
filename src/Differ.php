<?php

namespace Gendiff\Differ;

use Gendiff\Parsers;
use Gendiff\Formatter;

function isAssoc($arr)         // взял со Stackoverflow, чуток подправил
{
    if ((!is_array($arr)) or (array() === $arr)) return false;
    return array_keys($arr) !== range(0, count($arr) - 1);
}

function getChildren(array $array)         // весь смысл данной ф-ии - корректная индентация вложенных ассоц.массивов
{
    foreach ($array as $key => $value) {
        if (isAssoc($value)) {
            $result['  ' . $key] = getChildren($value);
        } else {
            $result['  ' . $key] = $value;
        }
    }
    return $result;
}

function buildDiff(array $array1, array $array2)
{
    foreach ($array1 as $key => $value) {
        if (array_key_exists($key, $array2)) {
            if ((isAssoc($array1[$key])) && (isAssoc($array2[$key]))) {
                $preResult[$key . '0 '] = buildDiff($array1[$key], $array2[$key]); 
            } elseif (isAssoc($array1[$key])) {
                 $preResult[$key . '1-'] = getChildren($array1[$key]);
                 $preResult[$key . '2+'] = $array2[$key];
            } elseif (isAssoc($array2[$key])) {
                 $preResult[$key . '1-'] = $array1[$key];
		 $preResult[$key . '2+'] = getChildren($array2[$key]);
           } elseif (($value === $array2[$key]) or (is_array($value))) {
                $preResult[$key . '0 '] = $value;
            } else {
      // смысл первой цифры в постфиксе - корректная сортировка полей поменявших значение
                $preResult[$key . '1-'] = $value;
                $preResult[$key . '2+'] = $array2[$key];
            }
        } else {
            if (isAssoc($array1[$key])) {
                $preResult[$key . '0-'] = getChildren($array1[$key]);
            } else {
            $preResult[$key . '0-'] = $value;
            }
        }
    }
    foreach ($array2 as $key => $value) {
        if (!array_key_exists($key, $array1)) {
            if (isAssoc($array2[$key])) {
                $preResult[$key . '0+'] = getChildren($array2[$key]);
            } else { 
                $preResult[$key . '0+'] = $value;
            }
        }
    }
    ksort($preResult);
    foreach ($preResult as $key => $value) {
        $prefix = $key[-1];
        $key = $prefix . " " . substr($key, 0, -2);
        $value = ((is_bool($value)) or ($value === null)) ? var_export($value, true) : $value;
        if ($value === "NULL") {
            $value = "null";
        }
//        $value = var_export($value, true);
        $result[$key] = $value;
    }
    return $result;
}

function differ($pathToFile1, $pathToFile2, $format)
{
    $inputArr1 = Parsers\parseToArray($pathToFile1);
    $inputArr2 = Parsers\parseToArray($pathToFile2);
    $diff = buildDiff($inputArr1, $inputArr2);
//    return $diff;
    return Formatter\stylish($diff, ' ', 2);
}
