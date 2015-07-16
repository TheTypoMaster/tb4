<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 24/06/2015
 * Time: 10:48 AM
 */

namespace TopBetta\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Contracts\Routing\Middleware;

class DestroySession {

    public function handle($request, Closure $next)
    {
        $response = $next($request);

        \Log::info('logging out!');
        Auth::logout();

        return $response;
    }
}