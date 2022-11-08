<?php

namespace Gendiff\Parsers;

use Symfony\Component\Yaml\Yaml;

function parseToArray($pathToFile)
{
    $str = file_get_contents($pathToFile, 0, null, null);
    if (pathinfo($pathToFile, PATHINFO_EXTENSION) === 'json') {
        $array = json_decode($str, true);
    } else {
        $array = Yaml::parse($str);
    }
    return $array;
}
