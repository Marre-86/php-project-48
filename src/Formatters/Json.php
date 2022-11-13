<?php

namespace Gendiff\Formatters\Json;

use Gendiff\Misc;

function iter(array $array, $prefix, $prefixCount, $previousItem, $previousStatus)
{   
    $result = array_map(function($key, $value) use ($prefix, $prefixCount, &$previousItem, &$previousStatus) {
        $realPrefix = str_repeat($prefix, $prefixCount);
        $childrenPrefix = str_repeat($prefix, $prefixCount + 2);
        $realKey = substr($key, 2);
        $status = ($realKey === $previousItem[0]) ? "updated" : getStatus($key);
        if (($previousStatus !== "removed") and ($previousStatus !== "added")) {
            $string = "{$realPrefix}\"{$realKey}\": {\n{$childrenPrefix}\"status\": \"{$status}\",\n{$childrenPrefix}\"value\": ";   
            $previousItem = (is_array($value)) ? [$realKey, '[complex value]'] : [$realKey, $value];
            $string .= (Misc\isAssoc($value))  ? "{\n" . iter($value, $prefix, $prefixCount + 4, $previousItem, $status) . "\n{$childrenPrefix}}" : "\"{$value}\"";
            $string .= "\n{$realPrefix}},";
        } else {
            $string = "{$realPrefix}\"{$realKey}\": \"{$value}\",";
        }
//        $string .= (($previousStatus !== "removed") and ($previousStatus !== "added")) ? "\n{$realPrefix}}," : "" ;
        return $string;
    }, array_keys($array), $array);
    $result = implode("\n", $result);
    return rtrim($result, ",");
}

function json($input, $replacer = ' ', $margin = 2)
{
    print_r($input);
    $result = iter($input, $replacer, $margin, [null, null], null);
    $result = "{\n" . $result . "\n}\n";
    echo $result;
    return "Check successfull!\n";
}

function getValue($inputValue)
{
    if (!Misc\isAssoc($inputValue)) {
        return $inputValue;
    } else {
       $result = array_map(function($key, $value) {
       $realKey = substr($key, 2);
            $string = "\"{$realKey}\": \"{$value}\",\n";
            return $string;
        }, array_keys($inputValue), $inputValue);
        $result = implode("\n", $result);
        return rtrim($result, ",");
    }
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
