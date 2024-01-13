<?php

namespace Thumbrise\Toolkit\Common\Functional;

use Closure;
use Illuminate\Support\Facades\Pipeline as PipelineReal;

class Pipeline
{
    public static function process(mixed $passable, array $operations)
    {
        $operationsCarried = array_map(self::carry(...), $operations);

        return PipelineReal::send($passable)->through($operationsCarried)->thenReturn();
    }

    private static function carry(callable $operation): Closure
    {
        return function ($passable, $next) use ($operation) {
            return $next($operation($passable));
        };
    }
}
