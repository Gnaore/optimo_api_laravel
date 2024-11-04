<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Str;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        $pathInfo = $request->getPathInfo();
        if (Str::contains($pathInfo, 'api')) {
          //return "yes";
        //   return response()->json([
        //     'errors'=> 'Authentication failed',
        //     'message' => "Credentials could not be retrieved. Please try again later."
        //   ], 401, [
        //     'Content-Type' => 'application/json'
        // ]);
        }else{
          return route('login_api');
        }

        // if (! $request->expectsJson()) {
        // }
    }
}
