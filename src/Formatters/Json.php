<?php

namespace Gendiff\Formatters\Json;

use Gendiff\Misc;

function json(array $input, string $replacer = ' ', int $margin = 2)
{
    $mainBody = iter($input, $replacer, $margin);
    $mainBodyWithBrackets = "{\n" . $mainBody . "\n}\n";
    $mainBodyTrimmed = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $mainBodyWithBrackets) ?? "false";
        // выяснилось что json_decode делает всю работу по отсеиванию первых элементов с тем же key
    $resultFilteredArray = json_decode($mainBodyTrimmed, true);
//  $result = removeRedundant($result);        // и использование отдельной функции которую начал писать не понадобилось
    $resultString = json_encode($resultFilteredArray, JSON_PRETTY_PRINT);
    return $resultString;
}

function iter(array $array, string $prefix, int $prefixCount, string $previousStatus = "")
{
    $result = array_map(function ($key, $value) use ($prefix, $prefixCount, &$previousStatus) {
        $realPrefix = str_repeat($prefix, $prefixCount);
        $childrenPrefix = str_repeat($prefix, $prefixCount + 2);
        $realKey = substr($key, 2);
        $realValue = (is_array($value) && array_key_exists('+value+', $value)) ? normalizeValue($value['+value+'], $prefix, $prefixCount) : normalizeValue($value, $prefix, $prefixCount); //phpcs:ignore
        $status = (is_array($value) && array_key_exists('+value+', $value)) ? "updated" : getStatus($key);
        if (($previousStatus !== "removed") and ($previousStatus !== "added")) {
            if ($status === "updated") {
                $updatedChildrenPrefix = str_repeat($prefix, $prefixCount + 4);
                $realPreviousValue = normalizeValue($value['+other value+'] ?? "+rmvThisLine+", $prefix, $prefixCount);
                $preString = "{$realPrefix}\"{$realKey}\": {\n{$childrenPrefix}\"status\": \"{$status}\",\n{$childrenPrefix}\"value\": {\n{$updatedChildrenPrefix}\"before\": {$realPreviousValue},\n{$updatedChildrenPrefix}\"after\": {$realValue}\n{$childrenPrefix}}"; // phpcs:ignore 
            } else {
                $prePreString = "{$realPrefix}\"{$realKey}\": {\n{$childrenPrefix}\"status\": \"{$status}\",\n{$childrenPrefix}\"value\": "; // phpcs:ignore 
                $preString = $prePreString .((Misc\isAssoc($value))  ? "{\n" . iter($value, $prefix, $prefixCount + 4, $status) . "\n{$childrenPrefix}}" : "{$realValue}"); //phpcs:ignore 
            }
            $string = $preString . "\n{$realPrefix}},";
        } else {
            $string = "{$realPrefix}\"{$realKey}\": {$realValue},";
        }
        return $string;
    }, array_keys($array), $array);
    return rtrim(implode("\n", $result), ",");
}

function normalizeValue(mixed $value, string $prefix, int $prefixCount)
{
    if (!is_array($value)) {
        $normalizedValue = ((in_array($value, ['true', 'false', 'null'], true)) or (is_numeric($value))) ?  $value : "\"{$value}\""; //phpcs:ignore
        return $normalizedValue;
    } elseif (!Misc\isAssoc($value)) {
        $openingBracket = "[\n";
        $prefixLevel1 = str_repeat($prefix, $prefixCount + 2);
        $prefixLevel2 = str_repeat($prefix, $prefixCount + 4);
        $closingBracket = "{$prefixLevel1}]";
        $mainBody = array_reduce($value, function ($acc, $item) use ($prefixLevel2) {
            return $acc . "{$prefixLevel2}\"{$item}\",\n";
        }, "");
        $mainBodyNoLastComma = substr_replace($mainBody, '', -2, 1);
        $normalizedValue = $openingBracket . $mainBodyNoLastComma . $closingBracket;
        return $normalizedValue;
    } else {
        return '"[complex value]"';
    }
}

function getStatus(string $input)
{
    if ($input[0] === " ") {
        $status = "saved";
    } elseif ($input[0] === "+") {
        $status = "added";
    } elseif ($input[0] === "-") {
        $status = "removed";
    } else {
        return null;
    }
    return $status;
}
