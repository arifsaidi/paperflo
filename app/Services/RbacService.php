<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class RbacService
{
    /**
     * Return role_id for a given session user (user_type + user_id).
     * If you support multiple roles per user, change this to return an array.
     */
    public function getRoleId(string $userType, int $userId, ?string $scopeType = null, ?int $scopeId = null): ?int
    {
        $q = DB::table('user_role')
            ->where('user_type', $userType)
            ->where('user_id', $userId);

        // Optional scope filtering (if you want conference-scoped RBAC later)
        if ($scopeType !== null) {
            $q->where('scope_type', $scopeType);
        }
        if ($scopeId !== null) {
            $q->where('scope_id', $scopeId);
        }

        return $q->value('role_id');
    }

    public function hasPermission(
        string $userType,
        int $userId,
        string $permissionCode,
        ?string $scopeType = null,
        ?int $scopeId = null
    ): bool {
        $roleId = $this->getRoleId($userType, $userId, $scopeType, $scopeId);
        if (!$roleId)
            return false;

        return DB::table('role_permission')
            ->join('permission', 'permission.permission_id', '=', 'role_permission.permission_id')
            ->where('role_permission.role_id', $roleId)
            ->where('permission.permission_code', $permissionCode)
            ->exists();
    }
}
