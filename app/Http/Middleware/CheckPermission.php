<?php

namespace App\Http\Middleware;

use App\Services\RbacService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function __construct(private RbacService $rbac)
    {
    }

    public function handle(Request $request, Closure $next, string $permissionCode)
    {
        $userType = session('user_type');
        $userId = (int) session('user_id');

        $allowed = $this->rbac->hasPermission($userType, $userId, $permissionCode);

        if (!$allowed) {
            abort(Response::HTTP_FORBIDDEN, 'Forbidden: insufficient permission.');
        }

        return $next($request);
    }
}
