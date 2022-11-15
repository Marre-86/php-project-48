<?php

namespace Gendiff\Misc;

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
    $arrMended = [];
    $array = [];
    $result = [];
    foreach ($arr as $val) {    // 15.11.2022 пару часов провозился чтобы в итоге вот этот блок кода написать
        if (strstr($val, PHP_EOL)) {
            $splitArr = explode("\n", $val);
            foreach ($splitArr as $v) {
                $arrMended[] = $v;
            }
        } else {
            $arrMended[] = $val;
        }
    }
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
