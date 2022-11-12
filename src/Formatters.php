<?php

namespace Gendiff\Formatters;

use Gendiff\Formatters\Stylish;
use Gendiff\Formatters\Plain;

function format($diff, $format)
{
    if ($format === 'stylish') {
        return Stylish\stylish($diff);
    } elseif ($format === 'plain') {
        return Plain\plain($diff);
    } else {
        return "Invalid format!\n";
    }
}
