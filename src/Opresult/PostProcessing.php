<?php

namespace Thumbrise\Toolkit\Opresult;


class PostProcessing
{
    public static function preventErrorPropagation(OperationResult|array $opresult, $env = 'production'): OperationResult|string
    {
        if (! app()->environment($env)) {
            if ($opresult instanceof OperationResult) {
                return $opresult;
            }

            return collect($opresult)->toJson();
        }

        if ($opresult instanceof OperationResult) {
            return self::preventErrorPropagationFromInstance($opresult);
        }

        return self::preventErrorPropagationFromArray($opresult);
    }

    private static function preventErrorPropagationFromArray(array $opresult): string
    {
        return collect($opresult)->only(['error_message', 'error_code', 'errorMessage', 'errorCode'])->toJson();
    }

    private static function preventErrorPropagationFromInstance(OperationResult $opresult): OperationResult
    {
        return $opresult->withoutErrorContext()->withLastErrorOnly();
    }
}
