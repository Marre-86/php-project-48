<?php

namespace Gendiff\Misc;

function isAssoc($arr)         // взял со Stackoverflow, чуток подправил
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

function removeRedundantItems(array $arr)
{
//    $array = [];
    $result = [];
    $arrMended = array_reduce($arr, function ($acc, $item) {
        if (strstr($item, PHP_EOL) !== false) {
            $splitArr = explode("\n", $item);
            $acc = array_merge($acc, $splitArr);
        } else {
            $acc[] = $item;
        }
        return $acc;
    }, []);
    foreach ($arrMended as $item) {
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
