<?php

namespace Thumbrise\Toolkit\Tests\Laravel\Skeleton\app\Service;

use Thumbrise\Toolkit\Opresult\OperationResult;
use Thumbrise\Toolkit\Tests\Laravel\Skeleton\app\Service\SomeNested\SomeNestedService;

class SomeService
{
    public OperationResult $somePropertyServiceError;

    public function __construct(private readonly SomeNestedService $nestedService)
    {
        $this->somePropertyServiceError = OperationResult::error('service property error', 'SERVICE_PROPERTY_ERROR');
    }

    public static function someStaticMethodError()
    {
        return OperationResult::error('service static method error', 'SERVICE_STATIC_METHOD_ERROR');
    }

    public function handle()
    {
        if (rand(0, 1)) {
            return OperationResult::error('service error', 'SERVICE_ERROR');
        }
        if (rand(0, 1)) {
            return $this->somePropertyServiceError;
        }

        return $this->nestedService->handle();
    }
}