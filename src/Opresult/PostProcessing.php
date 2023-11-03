<?php

namespace Thumbrise\Toolkit\Opresult;


class PostProcessing
{
    public static function preventErrorPropagation(OperationResult|array $opresult, $env = 'production'): OperationResult|array
    {
        if (! app()->environment($env)) {
            return $opresult;
        }

        if ($opresult instanceof OperationResult) {
            return self::preventErrorPropagationFromInstance($opresult);
        }

        return self::preventErrorPropagationFromArray($opresult);
    }

    private static function preventErrorPropagationFromArray(array $opresult): array
    {
        return collect($opresult)->only(['error_message', 'error_code', 'errorMessage', 'errorCode'])->toArray();
    }

    private static function preventErrorPropagationFromInstance(OperationResult $opresult): OperationResult
    {
        return $opresult->withoutErrorContext()->withLastErrorOnly();
    }
}
