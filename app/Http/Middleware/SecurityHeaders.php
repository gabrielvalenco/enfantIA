<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Add common security headers
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
        
        // Add Content Security Policy - ajustado para permitir fontes e ícones
        $csp = "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.bunny.net https://fonts.googleapis.com https://code.jquery.com; " .
               "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com https://fonts.bunny.net https://fonts.gstatic.com; " .
               "img-src 'self' data: https:; font-src 'self' https://fonts.googleapis.com https://fonts.gstatic.com https://fonts.bunny.net https://cdnjs.cloudflare.com data:; " .
               "connect-src 'self'; frame-src 'self'; frame-ancestors 'self'; form-action 'self';";
        
        $response->headers->set('Content-Security-Policy', $csp);

        // Cache control for non-static resources
        if (!$request->is('*.js') && !$request->is('*.css') && !$request->is('*.jpg') && !$request->is('*.png')) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', 'Fri, 01 Jan 1990 00:00:00 GMT');
        }

        return $response;
    }
}
