<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomCors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Log CORS request details for debugging
        \Log::debug('CORS middleware processing request', [
            'origin' => $request->header('Origin'),
            'method' => $request->method(),
            'path' => $request->path(),
            'headers' => $request->headers->all()
        ]);
        
        // Add CORS headers to all responses
        $response->headers->set('Access-Control-Allow-Origin', $request->header('Origin') ?? '*');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-XSRF-TOKEN');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
        $response->headers->set('Access-Control-Max-Age', '86400'); // 24 hours
        
        // Handle preflight OPTIONS requests
        if ($request->isMethod('OPTIONS')) {
            $response->setStatusCode(200);
            $response->setContent('');
            return $response;
        }
        
        return $response;
    }
}