<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OrganizerMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || auth()->user()->role !== 'organizer' || !auth()->user()->tenant_id) {
            abort(403, 'Akses ditolak. Khusus Penyelenggara (Organizer).');
        }

        return $next($request);
    }
}