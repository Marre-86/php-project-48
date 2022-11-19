<?php

namespace Gendiff\Formatters\Plain;

use Gendiff\Misc;

function plain(array $input)
{
    return iter($input, '', [null, null]);
}

function iter(array $input, string $path, array $previous)
{
    $result = array_map(function ($key, $value) use ($path, &$previous) {
        $string = null;
        $currentKey = substr(strval($key), 2);              // отбрасываем служебные первые два символа
        $currentValue = normalizeValue($value);
        if ((Misc\isAssoc($value)) and ($key[0] === " ")) {
            $pathExtended = $path . $currentKey . ".";
            $newPrevious = (is_array($value)) ? [$currentKey, '[complex value]'] : [$currentKey, $currentValue];
            $string = iter($value, $pathExtended, $newPrevious);
        } elseif ($previous[0] === $currentKey) {
            $string = "Property '{$path}{$currentKey}' was updated. From {$previous[1]} to {$currentValue}";
        } elseif ($key[0] === "+") {
            $string = "Property '{$path}{$currentKey}' was added with value: {$currentValue}";
        } elseif ($key[0] === "-") {
            $string = "Property '{$path}{$currentKey}' was removed";
        }
        $previous = (is_array($value)) ? [$currentKey, '[complex value]'] : [$currentKey, $currentValue]; // phpcs:ignore
        return $string;
    }, array_keys($input), $input);
    $result = Misc\removeRedundantItems(array_filter(Misc\flatten($result)));
    $string = implode("\n", $result);
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
