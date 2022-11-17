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

function removeRedundantItems(array $input)
{
    $arrMended = array_reduce($input, function ($acc, $item) {
        if (strstr($item, PHP_EOL) !== false) {
            $splitArr = explode("\n", $item);
            $acc = array_merge($acc, $splitArr);
        } else {
            $acc[] = $item;
        }
        return $acc;
    }, []);
    $wordsArrays = array_map(fn($line) => explode(" ", $line), $arrMended);
    $lineNumber = 0;
    $wordsArraysFiltered = array_reduce($wordsArrays, function ($acc, $line) use ($wordsArrays, &$lineNumber) {
        if ((isset($wordsArrays[$lineNumber + 1][1])) and ($wordsArrays[$lineNumber + 1][1] !== $line[1])) {
            $acc[] = $line;
        } elseif ($lineNumber === count($wordsArrays) - 1) {
            $acc[] = $line;
        }
        $lineNumber++;
        return $acc;
    }, []);
    $result = array_map(fn($line) => implode(" ", $line), $wordsArraysFiltered);
    return $result;
}
