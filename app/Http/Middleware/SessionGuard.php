<?php

namespace App\Http\Middleware;

use App\Models;
use Closure;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SessionGuard
{
    public function __construct(protected AuthFactory $auth)
    {
        $auth->shouldUse('session');
    }

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->auth->guard()->guest()) {
            return redirect('/session/login');
        }

        return $next($request);
    }
}
