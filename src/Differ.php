<?php

namespace Gendiff\Differ;

use Symfony\Component\Yaml\Yaml;

function differ($pathToFile1, $pathToFile2, $format)
{
    $str1 = file_get_contents($pathToFile1, 0, null, null);
    $str2 = file_get_contents($pathToFile2, 0, null, null);
    if ((pathinfo($pathToFile1, PATHINFO_EXTENSION) === 'json') && ((pathinfo($pathToFile2, PATHINFO_EXTENSION) === 'json'))) {  // phpcs:ignore
        $array1 = json_decode($str1, true);
        $array2 = json_decode($str2, true);
    } else {
        $array1 = Yaml::parse($str1);
        $array2 = Yaml::parse($str2);
    }
    foreach ($array1 as $key1 => $value1) {
        if (array_key_exists($key1, $array2)) {
            if ($value1 === $array2[$key1]) {
                $result[$key1 . '0 '] = $value1;
            } else {
      // смысл первой цифры в постфиксе - корректная сортировка полей поменявших значение
                $result[$key1 . '1-'] = $value1;
                $result[$key1 . '2+'] = $array2[$key1];
            }
        } else {
            $result[$key1 . '0-'] = $value1;
        }
    }
    foreach ($array2 as $key2 => $value2) {
        if (!array_key_exists($key2, $array1)) {
            $result[$key2 . '0+'] = $value2;
        }
    }
    ksort($result);
    $strResult = "{\n";
    foreach ($result as $key => $value) {
        $prefix = $key[-1];
        $key = substr($key, 0, -2);
        $value = (is_bool($value)) ? var_export($value, true) : $value;
        $strResult .= "  {$prefix} {$key}: {$value}\n";
    }
    $strResult .= "}\n";
    return $strResult;
}

//$pathToFile1 = 'files/file1.json';
//$pathToFile2 = 'files/file2.json';
//$pathToFile11 = '/home/marre/php-project-48/files/file1.json';
//$pathToFile12 = '/home/marre/php-project-48/files/file2.json';

//echo differ($str1, $str2);
