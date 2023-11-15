<?php

namespace Thumbrise\Toolkit\Tests\Laravel\Skeleton\app\Http\Controllers\Nested;

use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\Response;
use Thumbrise\Toolkit\Opresult\Errors;
use Thumbrise\Toolkit\Opresult\OperationResult;
use Thumbrise\Toolkit\Tests\Laravel\Skeleton\app\Http\Controllers\Controller;

class NestedController extends Controller
{
    #[Post(
        path: "/api/nested",
        summary: "test",
        tags: ["test"],
        responses: [
            new Response(response: 200, description: 'Успешная операция', content: new JsonContent()),
        ]
    )]
    public function test(): OperationResult
    {
        if (false) {
            return OperationResult::error('error 1', Errors::VALIDATION);
        }
        if (false) {
            return OperationResult::error('error 1', Errors::UNAUTHENTICATED);
        }
        return ret;
        return OperationResult::error('LOL', 'KEK');
        return OperationResult::success();
    }
}