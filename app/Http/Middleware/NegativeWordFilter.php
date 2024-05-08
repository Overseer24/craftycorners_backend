<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Sightengine\SightengineClient;
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
        $blockedDomains = file(storage_path('app/blocked_domains.txt'), FILE_IGNORE_NEW_LINES);
        $content = $request->input('content');
        $title = $request->input('title');
        $link = $request->input('link');

        foreach($negativeWords as $word){
            if (preg_match('/\b' . preg_quote($word, '/') . '\b/i', $content) || preg_match('/\b' . preg_quote($word, '/') . '\b/i', $title)){
                //return the also the inappropriate word
                return response()->json(['message' => 'The content contains inappropriate words. '.$word], 403);
            }
        }

        // Check if a link is present in the request
        if ($link) {
            // Extract the domain from the link
            $linkDomain = parse_url($link, PHP_URL_HOST);
            $linkDomain = strtolower($linkDomain);

            // Convert the blocked domains to lowercase
            $blockedDomains = array_map('strtolower', $blockedDomains);
            // Check if the domain is in the list of blocked domains
            if (in_array($linkDomain, $blockedDomains)) {
                return response()->json(['message' => 'This domain is blocked.'], 403);
            }
        }

        return $next($request);

}
}
