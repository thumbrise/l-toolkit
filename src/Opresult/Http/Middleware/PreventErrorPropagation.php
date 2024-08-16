<?php

namespace Thumbrise\Toolkit\Opresult\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Thumbrise\Toolkit\Opresult\Error;

use function app;

class PreventErrorPropagation
{
    public function handle(Request $request, Closure $next, $env = 'production')
    {
        if (app()->environment($env)) {
            Error::disableSensitiveDetails();
        }

        return $next($request);
    }
}
