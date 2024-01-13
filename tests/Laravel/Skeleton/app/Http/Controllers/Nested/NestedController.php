<?php

namespace Thumbrise\Toolkit\Tests\Laravel\Skeleton\app\Http\Controllers\Nested;

use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\Response;
use Thumbrise\Toolkit\Opresult\OperationResult;
use Thumbrise\Toolkit\Tests\Laravel\Skeleton\app\Http\Controllers\Controller;
use Thumbrise\Toolkit\Tests\Laravel\Skeleton\app\Service\SomeService;

class NestedController extends Controller
{
    private OperationResult $controllerPropertyError;

    public function __construct()
    {
        $this->controllerPropertyError = OperationResult::error('controller property error');
    }

    public static function controllerStaticMethodError()
    {
        return OperationResult::error('controller static method error', 'CONTROLLER_STATIC_METHOD_ERROR');
    }

    #[Post(
        path: "/api/nested",
        summary: "test",
        tags: ["test"],
        responses: [
            new Response(response: 200, description: 'Успешная операция', content: new JsonContent()),
        ]
    )]
    public function test(SomeService $someService): ?OperationResult
    {
        //this file/class
        if (rand(0, 1)) {
            return OperationResult::error('controller error', 'CONTROLLER_ERROR');
        }
        if (rand(0, 1)) {
            return null ?? OperationResult::error('controller coalesce error', 'CONTROLLER_COALESCE_ERROR');
        }
        if (rand(0, 1)) {
            return self::controllerStaticMethodError();
        }
        $variableError = OperationResult::error('error variable 1', 'CONTROLLER_VARIABLE_ERROR');
        if (rand(0, 1)) {
            return $variableError;
        }

        //another file/class
        if (rand(0, 1)) {
            return $someService->somePropertyServiceError;
        }
        if (rand(0, 1)) {
            return $someService::someStaticMethodError();
        }
        if (rand(0, 1)) {
            return SomeService::someStaticMethodError();
        }

        return $someService->handle();
    }
}