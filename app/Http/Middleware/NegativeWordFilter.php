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
        $negativeWords = explode(',', file_get_contents(storage_path('app/negative_words.txt')));
        $content = $request->input('content');
        $title = $request->input('title');

        foreach($negativeWords as $words){
            if (stripos($content,$words)!==false || stripos($title,$words)!==false){
                return response()->json(['message'=>'Post contains inappropriate words'],403);
            }
        }

        return $next($request);
    }
}
