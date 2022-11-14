<?php

namespace Gendiff\Formatters\Json;

use Gendiff\Misc;

function json($input, $replacer = ' ', $margin = 2)
{
    $result = iter($input, $replacer, $margin, [null, null], null);
    $result = "{\n" . $result . "\n}\n";
        // выяснилось что json_decode делает всю работу по отсеиванию первых элементов с тем же key
    $result = json_decode($result, true);
//  $result = removeRedundant($result);        // и использование отдельной функции которую начал писать не понадобилось
    $result = json_encode($result, JSON_PRETTY_PRINT);
    return $result;
}

function iter(array $array, $prefix, $prefixCount, $previousItem, $previousStatus)
{
    $result = array_map(function ($key, $value) use ($prefix, $prefixCount, &$previousItem, &$previousStatus) {
        $realPrefix = str_repeat($prefix, $prefixCount);
        $childrenPrefix = str_repeat($prefix, $prefixCount + 2);
        $realKey = substr($key, 2);
        $realValue = normalizeValue($value);
        $status = ($realKey === $previousItem[0]) ? "updated" : getStatus($key);
        if (($previousStatus !== "removed") and ($previousStatus !== "added")) {
            if ($status === "updated") {
                $updatedChildrenPrefix = str_repeat($prefix, $prefixCount + 4);
                $realPreviousValue = normalizeValue($previousItem[1]);
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

function normalizeValue($value)
{
    if (!is_array($value)) {
        $normalizedValue = (in_array($value, ['true', 'false', 'null'])) ? $value : "\"{$value}\"";
        if (intval($value)) {
            $normalizedValue = $value;
        }
    }
    return $normalizedValue ?? null;
}

function getStatus($input)
{
    if ($input[0] === " ") {
        $status = "saved";
    } elseif ($input[0] === "+") {
        $status = "added";
    } elseif ($input[0] === "-") {
        $status = "removed";
    }
    return $status;
}

function removeRedundant($input)
{
    $result = array_filter($input, function ($value, $key) {
        return ($value !== "Cat");
    }, ARRAY_FILTER_USE_BOTH);
    return $result;
}
