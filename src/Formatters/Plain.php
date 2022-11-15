<?php

namespace Gendiff\Formatters\Plain;

use Gendiff\Misc;

function plain($input)
{
    return iter($input, '', [null, null]);
}

function iter($input, $path, $previous)
{
    $result = array_map(function ($key, $value) use ($path, &$previous) {
        $string = null;
        $realKey = substr($key, 2);              // отбрасываем служебные первые два символа
        if (!is_array($value)) {
            $realValue = (in_array($value, ['true', 'false', 'null'])) ? $value : "'{$value}'";
        }
        $currentValue = (is_array($value)) ? '[complex value]' : $realValue;
        if ((Misc\isAssoc($value)) and ($key[0] === " ")) {
            $path .= $realKey . ".";
            $previous = (is_array($value)) ? [$realKey, '[complex value]'] : [$realKey, $realValue];
            $string = iter($value, $path, $previous);
        } elseif ($previous[0] === $realKey) {
             $string = "Property '{$path}{$realKey}' was updated. From {$previous[1]} to {$currentValue}";
        } elseif ($key[0] === "+") {
            $string = "Property '{$path}{$realKey}' was added with value: {$currentValue}";
        } elseif ($key[0] === "-") {
            $string = "Property '{$path}{$realKey}' was removed";
        }
        $previous = (is_array($value)) ? [$realKey, '[complex value]'] : [$realKey, $realValue]; // phpcs:ignore
        return $string;
    }, array_keys($input), $input);
    $result1 = Misc\removeRedundantItems(array_filter(Misc\flatten($result)));
    $string = implode("\n", $result1);
    return $string;
}
