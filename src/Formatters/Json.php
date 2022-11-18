<?php

namespace Gendiff\Formatters\Json;

use Gendiff\Misc;

function json(array $input, string $replacer = ' ', int $margin = 2)
{
    $mainBody = iter($input, $replacer, $margin, [null, null], "");
    $mainBodyWithBrackets = "{\n" . $mainBody . "\n}\n";
        // выяснилось что json_decode делает всю работу по отсеиванию первых элементов с тем же key
    $resultFilteredArray = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $mainBodyWithBrackets), true);
//  $result = removeRedundant($result);        // и использование отдельной функции которую начал писать не понадобилось
    $resultString = json_encode($resultFilteredArray, JSON_PRETTY_PRINT);
    return $resultString;
}

function iter(array $array, string $prefix, int $prefixCount, array $previousItem, string $previousStatus)
{
    $result = array_map(function ($key, $value) use ($prefix, $prefixCount, &$previousItem, &$previousStatus) {
        $realPrefix = str_repeat($prefix, $prefixCount);
        $childrenPrefix = str_repeat($prefix, $prefixCount + 2);
        $realKey = substr($key, 2);
        $realValue = normalizeValue($value, $prefix, $prefixCount);
        $status = ($realKey === $previousItem[0]) ? "updated" : getStatus($key);
        if (($previousStatus !== "removed") and ($previousStatus !== "added")) {
            if ($status === "updated") {
                $updatedChildrenPrefix = str_repeat($prefix, $prefixCount + 4);
                $realPreviousValue = normalizeValue($previousItem[1], $prefix, $prefixCount);
                $string = "{$realPrefix}\"{$realKey}\": {\n{$childrenPrefix}\"status\": \"{$status}\",\n{$childrenPrefix}\"value\": {\n{$updatedChildrenPrefix}\"before\": {$realPreviousValue},\n{$updatedChildrenPrefix}\"after\": {$realValue}\n{$childrenPrefix}}"; // phpcs:ignore 
            } else {
                $string = "{$realPrefix}\"{$realKey}\": {\n{$childrenPrefix}\"status\": \"{$status}\",\n{$childrenPrefix}\"value\": "; // phpcs:ignore 
                $previousItem = (is_array($value)) ? [$realKey, '[complex value]'] : [$realKey, $value];
                $string .= (Misc\isAssoc($value))  ? "{\n" . iter($value, $prefix, $prefixCount + 4, $previousItem, $status) . "\n{$childrenPrefix}}" : "{$realValue}"; //phpcs:ignore 
            }
            $string .= "\n{$realPrefix}},";
        } else {
            $string = "{$realPrefix}\"{$realKey}\": {$realValue},";
        }
        return $string;
    }, array_keys($array), $array);
    $result = implode("\n", $result);
    return rtrim($result, ",");
}

function normalizeValue($value, string $prefix, int $prefixCount)
{
    if (!is_array($value)) {
        $normalizedValue = ((in_array($value, ['true', 'false', 'null'], false)) or (is_numeric($value))) ?  $value : "\"{$value}\""; //phpcs:ignore
        return $normalizedValue;
    } elseif (!Misc\isAssoc($value)) {
        $openingBracket = "[\n";
        $prefixLevel1 = str_repeat($prefix, $prefixCount + 2);
        $prefixLevel2 = str_repeat($prefix, $prefixCount + 4);
        $closingBracket = "{$prefixLevel1}]";
        $mainBody = array_reduce($value, function ($acc, $item) use ($prefixLevel2) {
            $acc .= "{$prefixLevel2}\"{$item}\",\n";
            return $acc;
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
