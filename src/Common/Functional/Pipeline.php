<?php

namespace Thumbrise\Toolkit\Common\Functional;

use Closure;
use Illuminate\Support\Facades\Pipeline as PipelineReal;
use Thumbrise\Toolkit\Opresult\OperationResult;

class Pipeline
{


    /**
     * Прогоняет через трубу a | b | c | d | etc и возвращает результат.
     * В случае возврата из функций OperationResult автоматически распаковывает ->data и посылает в качестве следующего passable в трубе
     *
     * @param mixed $passable
     * @param array $operations
     *
     * @return mixed|OperationResult|null
     */
    public static function process(mixed $passable, array $operations): mixed
    {
        $operationsCarried = array_map(self::carry(...), $operations);

        return PipelineReal::send($passable)->through($operationsCarried)->thenReturn();
    }


    protected static function carry(callable $operation): Closure
    {
        return function ($passable, $next) use ($operation) {
            if ($passable instanceof OperationResult) {
                $passable = $passable->data;
            }

            $result = $operation($passable);
            if ($result instanceof OperationResult && $result->isError()) {
                return $result;
            }

            return $next($result);
        };
    }


}
