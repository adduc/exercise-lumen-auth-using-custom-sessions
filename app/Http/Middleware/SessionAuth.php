<?php

namespace App\Http\Middleware;

use App\Models;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SessionAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $user_id = $request->session()->get('user_id');
        $user = $user_id ? Models\User::find($user_id) : null;

        if (!$user) {
            return redirect('/session/login');
        }

        $request->setUserResolver(fn () => $user);
        return $next($request);
    }
}
