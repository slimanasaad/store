<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HasCenterPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();
        $centerId = $request->center_id;

        if (! $centerId) {
            return response()->json(['message' => 'لم يتم تحديد المركز'], 400);
        }

        $hasPermission = $user->roles()
            ->wherePivot('center_id', $centerId)
            ->with('permissions')
            ->get()
            ->pluck('permissions')
            ->flatten()
            ->pluck('name')
            ->contains($permission);

        if (! $hasPermission) {
            return response()->json(['message' => 'غير مصرح لك بتنفيذ هذا الإجراء'], 403);
        }

        return $next($request);
    }
}
