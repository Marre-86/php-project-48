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
        if (!is_array($value)) {
            $value = (is_bool($value)) ? var_export($value, true) : $value;
            $formattedLine = "{$realPrefix}{$key}: {$value}\n";
        } else {
            $prefixCount += 4;
            $value = iter($value, $prefix, $prefixCount);
            $prefixCount -= 4;
            $formattedLine = "{$realPrefix}{$key}: {$value}";
        }
        return $formattedLine;
    }, array_keys($array), $array);
    $formattedString = $openingBracket . implode("", $formattedArray) . $closingBracketPrefix . "}\n";
    return $formattedString;
}
