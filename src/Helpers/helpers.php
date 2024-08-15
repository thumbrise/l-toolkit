<?php

if (! function_exists('ddraw')) {
    function ddraw(mixed ...$values): never
    {
        /** @phpstan-ignore-next-line */
        $trace = debug_backtrace(! DEBUG_BACKTRACE_PROVIDE_OBJECT | DEBUG_BACKTRACE_IGNORE_ARGS);
        $where = $trace[0];
        echo PHP_EOL.$where['file'].':'.$where['line'].PHP_EOL;

        foreach ($values as $value) {
            print_r($value);
        }

        exit;
    }
}
