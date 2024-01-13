<?php

namespace Thumbrise\Toolkit\Tests\Laravel\Skeleton\app\Service\SomeNested;

use Thumbrise\Toolkit\Opresult\OperationResult;

class SomeNestedService
{
    public function handle()
    {
        if (rand(0, 1)) {
            return OperationResult::error('service nested error 1', 'SERVICE_INTERNAL_ERROR_1');
        }
        if (rand(0, 1)) {
            return OperationResult::error('service nested error 2', 'SERVICE_INTERNAL_ERROR_2');
        }
        if (rand(0, 1)) {
            return OperationResult::error('service nested error 3', 'SERVICE_INTERNAL_ERROR_3');
        }

        return OperationResult::success();
    }
}