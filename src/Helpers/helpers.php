<?php


if (! function_exists('ddraw')) {
    function ddraw(mixed...$values): never
    {
        foreach ($values as $value) {
            print_r($value);
        }

        die();
    }
}
