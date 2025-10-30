<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HasCenterRole
{
    public function handle(Request $request, Closure $next, string $roleName): Response
    {
        $user = $request->user();
        $centerId = $request->center_id;

        if (! $centerId) {
            return response()->json(['message' => 'لم يتم تحديد المركز'], 400);
        }

        // التحقق إذا كان المستخدم لديه الدور المطلوب في المركز المطلوب
        $hasRole = $user->roles()
            ->wherePivot('center_id', $centerId)
            ->where('name', $roleName)
            ->exists();

        if (! $hasRole) {
            return response()->json(['message' => 'ليس لديك الدور المطلوب في هذا المركز'], 403);
        }

        return $next($request);
    }
}
