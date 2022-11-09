<?php

namespace Gendiff\Formatter;

function iter(array $array, $prefix, $prefixCount)
{
//    echo $prefixCount . array_keys($array)[0] . "\n";
    $strResult = "{\n";
    $realPrefix = str_repeat($prefix, $prefixCount);
    $bracketPrefix = str_repeat($prefix, $prefixCount - 2);
    foreach ($array as $key => $value) {
        if (!is_array($value)) {
            $value = (is_bool($value)) ? var_export($value, true) : $value;
            $strResult .= rtrim("{$realPrefix}{$key}: {$value}", " ") . "\n";
        } else {
            $prefixCount += 4;
            $value = iter($value, $prefix, $prefixCount);
            $prefixCount -= 4;
            $strResult .= "{$realPrefix}{$key}: {$value}";
        }
    }
    $strResult .= $bracketPrefix . "}\n";
    return $strResult;
}

function stylish($input, $replacer = ' ', $margin = 1)
{
    if (!is_array($input)) {
        $result = (is_bool($input)) ? var_export($input, true) : $input;
        return $result;
    } else {
//        $prefix = '';
//        for ($i = 1; $i <= $margin; $i++) {
//            $prefix .= $replacer;
//        }
        $result = iter($input, $replacer, $margin);
//       return rtrim($result);       ХЗ надо EOL в конце или нет
        return $result;
    }
}
