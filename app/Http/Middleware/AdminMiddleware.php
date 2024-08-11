<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(!$request->user() || !$request->user()->role == 1) {
            return response()->json([
                "status" => Response::HTTP_UNAUTHORIZED,
                "message" => "Tidak memiliki hak akses admin",
            ], Response::HTTP_UNAUTHORIZED);
        }
        return $next($request);
    }
}
