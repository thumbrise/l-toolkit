<?php

namespace Thumbrise\Toolkit\Opresult\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Thumbrise\Toolkit\Opresult\OperationResult;
use Thumbrise\Toolkit\Opresult\PostProcessing;

class PreventErrorPropagation
{


    public function handle(Request $request, Closure $next, $env='production')
    {
        /** @var Response $response */
        $response = $next($request);

        $opresult = $response->getOriginalContent();
        if ($opresult instanceof OperationResult || is_array($opresult)) {
            $response->setContent(PostProcessing::preventErrorPropagation($opresult, $env));
        }

        return $response;
    }


}
