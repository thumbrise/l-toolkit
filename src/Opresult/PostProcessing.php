<?php

namespace Thumbrise\Toolkit\Opresult;


class PostProcessing
{
    public static function preventErrorPropagation(OperationResult $operationResult): OperationResult
    {
        if (app()->isProduction()) {
            return $operationResult->withoutErrorContext()->withLastErrorOnly();
        }

        return $operationResult;
    }
}
