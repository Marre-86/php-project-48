<?php

namespace Gendiff\Parsers;

use Symfony\Component\Yaml\Yaml;

function parseToArray(string $pathToFile)
{
    $str = file_get_contents($pathToFile, false, null, 0);
    if ($str !== false) {
        if (pathinfo($pathToFile, PATHINFO_EXTENSION) === 'json') {
            $array = json_decode($str, true);
        } else {
            $array = Yaml::parse($str);
        }
        return $array;
    }
}
