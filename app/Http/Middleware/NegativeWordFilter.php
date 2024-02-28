<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NegativeWordFilter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $negativeWords = ['nigga','fuck'];
        $content = $request->input('content');

        foreach($negativeWords as $words){
            if (stripos($content,$words)!==false){
                return response()->json(['error'=>'Post Contains Negative Words']);
            }
        }



        return $next($request);
    }
}
