<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use App\Models;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Router;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', fn () => <<<HTML
    <h1>Lumen Auth: Session Example</h1>
    <ul>
        <li><a href="/session/flush">Clear Session</a></li>
        <li><a href="/session/invalidate">Clear Invalidate</a></li>
        <li><a href="/session/register">Register</a></li>
        <li><a href="/session/login">Login</a></li>
        <li><a href="/session/status">Authentication Status</a></li>
    </ul>
    <h2>Gates</h2>
    <ul>
        <li><a href="/session/gate/in-route">In-Route</a></li>
        <li><a href="/session/gate/custom-middleware">Custom Middleware</a></li>
        <li><a href="/session/gate/custom-guard">Custom Guard</a></li>
    </ul>
HTML);

$router->group(['middleware' => ['session']], function (Router $router) {
    $router->get('/session/flush', function (Request $request) {
        $request->session()->flush();
        return 'Session flushed';
    });

    $router->get('/session/invalidate', function (Request $request) {
        $request->session()->invalidate();
        return 'Session invalidated';
    });

    $router->get('/session/register', fn () => view('session-register'));

    $router->post('/session/register', function (Request $request) use ($router) {
        $user = Models\User::create([
            'username' => $request->input('user'),
            'password' => $request->input('pass'),
        ]);

        $request->session()->put('user_id', $user->id);
        return redirect('/session/gate/in-route');
    });

    $router->get('/session/login', fn () => view('session-login'));

    $router->post('/session/login', function (Request $request) {
        $user = Models\User::firstWhere('username', $request->input('user'));

        if (!$user || !password_verify($request->input('pass'), $user->password)) {
            return redirect('/session/login');
        }

        $request->session()->put('user_id', $user->id);
        return redirect('/session/gate/in-route');
    });

    $router->get('/session/status', function (Request $request) {
        $user_id = $request->session()->get('user_id');
        $user = $user_id ? Models\User::find($user_id) : null;

        return $user ? 'Logged In as ' . $user->username : 'Not Logged In';
    });

    $router->get('/session/gate/in-route', function (Request $request) {
        $user_id = $request->session()->get('user_id');
        $user = $user_id ? Models\User::find($user_id) : null;

        return $user ?? redirect('/session/register');
    });

    $router->get('/session/gate/custom-middleware', ['middleware' => 'session-auth', function (Request $request) {
        return $request->user();
    }]);

    $router->get('/session/gate/custom-guard', ['middleware' => 'session-guard', function (Request $request) {
        return $request->user();
    }]);
});
