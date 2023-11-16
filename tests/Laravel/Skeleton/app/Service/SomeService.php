<?php

namespace Thumbrise\Toolkit\Tests\Laravel\Skeleton\app\Service;

use Thumbrise\Toolkit\Opresult\OperationResult;
use Thumbrise\Toolkit\Tests\Laravel\Skeleton\app\Service\SomeNested\SomeNestedService;

class SomeService
{
    public function __construct(private readonly SomeNestedService $nestedService)
    {
    }

    public function handle()
    {
        if (false) {
            return OperationResult::error('service error 1', 'SERVICE_ERROR_1');
        }
        if (false) {
            return OperationResult::error('service error 2', 'SERVICE_ERROR_2');
        }
        if (false) {
            return OperationResult::error('service error 3', 'SERVICE_ERROR_3');
        }

        return $this->nestedService->handle();
    }
}