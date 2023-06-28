<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;
class RoleHasPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request)
    {
        // $routeName = $request->route()->getName();
        // $permission = Auth::user()->permissions()->where('route_name', $routeName)->first();
        // if ( !empty($permission)){
        //     return response()->json([
        //         'message' => 'Something went wrong'
        //     ]);                       
        // }
        // return $next($request);
    }

    
}
