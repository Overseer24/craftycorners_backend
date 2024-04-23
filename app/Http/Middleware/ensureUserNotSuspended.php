<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ensureUserNotSuspended
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        //fetch suspension date base on report
        $user=Auth::user();
        if($user && $user->type === 'suspended'){
            $unsuspendDate = $user->reportedPosts()->where('resolution_option', 'suspend')->first()->unsuspend_date;
            //logout user

            return response()->json([
                'message' => 'You are suspended until '.$unsuspendDate
            ], 403);
        }

        return $next($request);
    }
}
