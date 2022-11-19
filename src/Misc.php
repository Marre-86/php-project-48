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

function removeRedundantItems(array $input)
{
    $arrMended = array_reduce($input, function ($acc, $item) {
        return array_merge($acc, (strstr($item, PHP_EOL) !== false) ? explode("\n", $item) : [$item]);
    }, []);
    $wordsArrays = array_map(fn($line) => explode(" ", $line), $arrMended);
    $lineNumber = 0;
    $wordsArraysFiltered = array_reduce($wordsArrays, function ($acc, $line) use ($wordsArrays, &$lineNumber) {
        $accNew = $acc;
        if ((isset($wordsArrays[$lineNumber + 1][1])) and ($wordsArrays[$lineNumber + 1][1] !== $line[1])) {
            $accNew[] = $line;
        } elseif ($lineNumber === count($wordsArrays) - 1) {
            $accNew[] = $line;
        }
        $lineNumber++;
        return $accNew;
    }, []);
    $result = array_map(fn($line) => implode(" ", $line), $wordsArraysFiltered);
    return $result;
}
