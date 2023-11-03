<?php

namespace Thumbrise\Toolkit\Common\Http\Middleware;


use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Thumbrise\Toolkit\Util\ArrayUtil;

class ResponseToCamel
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        if ($response instanceof JsonResponse) {
            $raw = json_decode($response->content(), true);
            $converted = ArrayUtil::keysToCamel($raw);
            $response->setData($converted);
        }

        return $response;
    }
}
