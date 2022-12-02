<?php

namespace Gendiff\Misc;

function isAssoc(mixed $arr)         // взял со Stackoverflow, чуток подправил
{
    if ((!is_array($arr)) or (array() === $arr)) {
        return false;
    }
    return array_keys($arr) !== range(0, count($arr) - 1);
}

function flatten(array $tree)
{
    $result = array_reduce($tree, function ($acc, $item) {
        $newValue = is_array($item) ? flatten($item) : $item;
        return array_merge($acc, is_array($newValue) ? $newValue : [$newValue]);
    }, []);
    return $result;
}
