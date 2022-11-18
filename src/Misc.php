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
        $accNew = $acc;
        $newValue = is_array($item) ? flatten($item) : $item;
        if (!is_array($newValue)) {
            $accNew[] = $newValue;
        } else {
            $accNew = array_merge($acc, $newValue);
        }
        return $accNew;
    }, []);
    return $result;
}

function removeRedundantItems(array $input)
{
    $arrMended = array_reduce($input, function ($acc, $item) {
        $accNew = $acc;
        if (strstr($item, PHP_EOL) !== false) {
            $splitArr = explode("\n", $item);
            $accNew = array_merge($acc, $splitArr);
        } else {
            $accNew[] = $item;
        }
        return $accNew;
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
