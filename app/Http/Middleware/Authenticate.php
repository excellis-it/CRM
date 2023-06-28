<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;


class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    // protected function redirectTo($request)
    // {
    //     if (! $request->expectsJson()) {
    //         return route('login');
    //     }
    // }

    protected function unauthenticated($request, array $guards)
    {
        abort(response()->json(
            [
                'status' => false,
                'code' => 401,
                'message' => 'UnAuthenticated',
            ], 401));
    }

    protected function permissionDenied($request)
    {
        abort(response()->json(
            [
                'status' => false,
                'code' => 401,
                'message' => 'Permission Denied',
            ], 401));
        // $routeName = Request::route()->getName();
        // $permission = $user->permissions()->where('route_name', $routeName)->first();
        // if ( !empty($permission)){
        //     return redirect()->back();                        
        // }
        // return $next($request);
    }
}
