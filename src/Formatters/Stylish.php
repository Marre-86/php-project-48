<?php

namespace Gendiff\Formatters\Stylish;

function stylish(mixed $input, string $replacer = ' ', int $margin = 2)
{
    if (!is_array($input)) {
        $result = (is_bool($input)) ? var_export($input, true) : $input;
        return $result;
    } else {
        $result = iter($input, $replacer, $margin);
        return rtrim($result, "\r\n");
    }
}

function iter(array $array, string $prefix, int $prefixCount)
{
    $openingBracket = "{\n";
    $realPrefix = str_repeat($prefix, $prefixCount);
    $closingBracketPrefix = str_repeat($prefix, $prefixCount - 2);
    $formattedArray = array_map(function ($key, $value) use ($realPrefix, $prefix, $prefixCount) {
        $realValue = (is_array($value) && array_key_exists('+value+', $value)) ? $value['+value+'] : $value;
        if (!is_array($realValue)) {
            $formattedValue = (is_bool($realValue)) ? var_export($realValue, true) : $realValue;
            $formattedLine = "{$realPrefix}{$key}: {$formattedValue}\n";
        } else {
            $newPrefixCount = $prefixCount + 4;
            $formattedValue = iter($realValue, $prefix, $newPrefixCount);
            $formattedLine = "{$realPrefix}{$key}: {$formattedValue}";
        }
        return $formattedLine;
    }, array_keys($array), $array);
    $formattedString = $openingBracket . implode("", $formattedArray) . $closingBracketPrefix . "}\n";
    return $formattedString;
}
