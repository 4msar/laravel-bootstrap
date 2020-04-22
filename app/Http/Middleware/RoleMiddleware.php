<?php

namespace App\Http\Middleware;

use Closure;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|array  $role
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        if( $user = $request->user() ){
            $roles = pipeToArray($role);
            if( $user->is($roles) ){
                return $next($request);
            }else{
                return abort(403, "Permission Denied!");
            }
        }else{
            return redirect()->route('login');
        }
    }
}
