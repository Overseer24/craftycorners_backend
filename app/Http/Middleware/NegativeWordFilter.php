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
            if (stripos($content,$word)!==false || stripos($title,$word)!==false){
                return response()->json(['message'=>'Post contains inappropriate words'],403);
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

        if($request->hasFile('image')){
            $client = new SightengineClient(env('SIGHTENGINE_USER'), env('SIGHTENGINE_SECRET'));
            $output = $client->check(['nudity-2.0', 'offensive', 'scam', 'tobacco', 'wad', 'offensive', 'gambling', 'gore'])
                ->set_file($request->file('image'));

//            dd($output);
            if ($output->nudity->sexual_activity >= 0.5 ||
                $output->nudity->sexual_display >= 0.5 ||
                $output->nudity->erotica >= 0.5 ||
                $output->offensive->prob >= 0.5 ||
                $output->gore->prob >= 0.5 ||
                $output->gambling->prob >= 0.5 ||
                $output->tobacco->prob >= 0.5 ||
                $output->weapon>= 0.5 ||
                $output->alcohol>= 0.5 ||
                $output->drugs>= 0.5 ){
                return response()->json(['message' => 'The image contains inappropriate content.'], 403);
            }

        }
        return $next($request);

}
}
