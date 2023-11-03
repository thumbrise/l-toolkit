<?php

namespace Thumbrise\Toolkit\Opresult\Http;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Thumbrise\Toolkit\Opresult\OperationResult;
use Thumbrise\Toolkit\Opresult\PostProcessing;

class PreventErrorPropagation
{
    public function handle(Request $request, Closure $next)
    {
        /** @var Response $response */
        $response = $next($request);

        $opResult = $response->getOriginalContent();
        if (! $opResult instanceof OperationResult) {
            return $response;
        }

        $response->setContent(PostProcessing::preventErrorPropagation($opResult));

        return $response;
    }
}
