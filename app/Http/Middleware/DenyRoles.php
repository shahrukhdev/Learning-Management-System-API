<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Blocks users who have any of the given roles, e.g. `deny.role:Student`.
 *
 * Used on per-student and write endpoints (Hifz, attendance) that take a
 * student or class id, so a student cannot change the id in the URL to see
 * or change other students' data. Students use their own "my" endpoints.
 */
class DenyRoles
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if ($user && $user->hasAnyRole($roles)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not allowed to access this resource.',
                'errors' => [],
            ], 403);
        }

        return $next($request);
    }
}
