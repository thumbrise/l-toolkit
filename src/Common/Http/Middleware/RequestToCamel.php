<?php

namespace Thumbrise\Toolkit\Common\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Thumbrise\Toolkit\Util\ArrayUtil;

class RequestToCamel
{
    public function handle(Request $request, Closure $next)
    {
        $converted = ArrayUtil::keysToCamel($request->input());
        $request   = $request->replace($converted);

        return $next($request);
    }
}
