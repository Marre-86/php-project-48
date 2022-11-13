<?php

namespace Gendiff\Misc;

function is_absolute_path($path)
{
    if ($path === null || $path === '') {
        throw new Exception("Empty path");
    }
    return $path[0] === DIRECTORY_SEPARATOR || preg_match('~\A[A-Z]:(?![^/\\\\])~i', $path) > 0;
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
