<?php

namespace Gendiff\Formatters;

use Gendiff\Formatters\Stylish;
use Gendiff\Formatters\Plain;
use Gendiff\Formatters\Json;

function format(array $diff, string $format)
{
    if ($format === 'stylish') {
        return Stylish\stylish($diff);
    } elseif ($format === 'plain') {
        return Plain\plain($diff);
    } elseif ($format === 'json') {
        return Json\json($diff);
    } else {
        return "Invalid format!\n";
    }
}
