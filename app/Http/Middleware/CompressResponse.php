<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CompressResponse
{
    /**
     * Handle an incoming request and compress the response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only compress if client accepts gzip
        if (strpos($request->header('Accept-Encoding', ''), 'gzip') !== false) {
            
            // Get content
            $content = $response->getContent();
            
            // Only compress if content is large enough (> 1KB)
            if (strlen($content) > 1024 && function_exists('gzencode')) {
                
                // Compress with gzip
                $compressed = gzencode($content, 6); // Level 6 = good balance
                
                // Set compressed content and headers
                $response->setContent($compressed);
                $response->header('Content-Encoding', 'gzip');
                $response->header('Content-Length', strlen($compressed));
                $response->header('Vary', 'Accept-Encoding');
            }
        }

        return $response;
    }
}
