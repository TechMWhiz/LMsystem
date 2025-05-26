<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CorsMiddleware
{
    public function handle(Request $request, Closure $next)
{
    $allowedOrigins = [
        'http://localhost:3000', // Local dev
        'https://frontend-hjb781jyt-claireskylights-projects.vercel.app/', //  Vercel URL
        
    ];

    $origin = $request->headers->get('Origin');

    if (in_array($origin, $allowedOrigins)) {
        return $next($request)
            ->header('Access-Control-Allow-Origin', $origin)
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
            ->header('Access-Control-Allow-Credentials', 'true');
    }

    return $next($request);
}
}