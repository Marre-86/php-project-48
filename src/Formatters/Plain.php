<?php

namespace Gendiff\Formatters\Plain;

function plain($input)
{
    return iter($input, '', [null, null]) . "\n";
}

function iter($input, $path, $previous)
{
    $result = array_map(function ($key, $value) use ($path, &$previous) {
        $string = null;
        $realKey = substr($key, 2);              // отбрасываем служебные первые два символа
        if (!is_array($value)) {
            $realValue = (in_array($value, ['true', 'false', 'null'])) ? $value : "'{$value}'";
        }
        $currentValue = (isAssoc($value)) ? '[complex value]' : $realValue;
        if ((isAssoc($value)) and ($key[0] === " ")) {
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
    $result1 = removeRedundantItems(array_filter(flatten($result)));
    $string = implode("\n", $result1);
    return $string;
}

function isAssoc($arr)         // взял со Stackoverflow, чуток подправил
{
    if ((!is_array($arr)) or (array() === $arr)) {
        return false;
    }
    return array_keys($arr) !== range(0, count($arr) - 1);
}

function flatten($tree)
{
    $result = array_reduce($tree, function ($acc, $item) {
        $newValue = is_array($item) ? flatten($item) : $item;
        if (!is_array($newValue)) {
            $acc[] = $newValue;
        } else {
            foreach ($newValue as $item) {
                $acc[] = $item;
            }
        }
        return $acc;
    }, []);
    return $result;
}

function removeRedundantItems($arr)
{
    $array = [];
    $result = [];
    foreach ($arr as $item) {
        $line = explode(" ", $item);
        $array[] = $line;
    }
    for ($i = 0; $i < count($array); $i++) {
        if ((isset($array[$i + 1][1])) and ($array[$i][1] === $array[$i + 1][1])) {
            unset($array[$i]);
        }
    }
    foreach ($array as $item) {
        $string = implode(" ", $item);
        $result[] = $string;
    }
    return $result;
}
