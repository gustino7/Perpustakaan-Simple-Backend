<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthorizeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        $book = $request->route('buku');

        if ($user->role === 1) {
            return $next($request);
        }

        if ($book->user_id !== $user->id) {
            return response()->json([
                'status' => Response::HTTP_UNAUTHORIZED,
                'message' => 'Tidak memiliki akses ke data buku ini'
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
