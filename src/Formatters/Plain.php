<?php

namespace Gendiff\Formatters\Plain;

use Gendiff\Misc;

function plain(array $input)
{
    return iter($input);
}

function iter(array $input, string $path = '')
{
    $result = array_map(function ($key, $value) use ($path) {
        $string = null;
        $currentKey = substr(strval($key), 2);              // отбрасываем служебные первые два символа
        $realValue = (is_array($value) && array_key_exists('+value+', $value)) ? $value['+value+'] : $value;
        $currentValue = normalizeValue($realValue);
        $previousValue = (is_array($value) && array_key_exists('+other value+', $value)) ? normalizeValue($value['+other value+']) : null; // phpcs:ignore
        if ((Misc\isAssoc($value)) and ($key[0] === " ")) {
            $pathExtended = $path . $currentKey . ".";
            return iter($value, $pathExtended);
        } elseif (is_array($value) && array_key_exists('+removeThisLine+', $value)) {
            return "";
        } elseif (is_array($value) && array_key_exists('+other value+', $value)) {
            return "Property '{$path}{$currentKey}' was updated. From {$previousValue} to {$currentValue}";
        } elseif ($key[0] === "+") {
            return "Property '{$path}{$currentKey}' was added with value: {$currentValue}";
        } elseif ($key[0] === "-") {
            return "Property '{$path}{$currentKey}' was removed";
        }
    }, array_keys($input), $input);
    $string = implode(PHP_EOL, array_filter(Misc\flatten($result)));
    return $string;
}

function normalizeValue(mixed $value)
{
    if (is_numeric($value)) {
        return $value;
    } elseif (!is_array($value)) {
        $quotedValue = (in_array($value, ['true', 'false', 'null'], true)) ? $value : "'{$value}'";
        return $quotedValue;
    } else {
        return '[complex value]';
    }
}
