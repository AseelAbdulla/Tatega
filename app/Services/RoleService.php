<?php

namespace App\Services;

use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class RoleService
{
    /**
     * Get all roles with permissions.
     */
    public function getAllRoles()
    {
        return Role::with('permissions')
            ->orderBy('id')
            ->get();
    }

    /**
     * Create a new role.
     */
    public function createRole(array $data)
    {
        return Role::create([
            'name' => $data['name'],
            'guard_name' => 'sanctum',
            'display_name' => $data['display_name'] ?? null,
        ]);
    }

    /**
     * Get role by ID.
     */
    public function getRoleById($id)
    {
        return Role::with('permissions')->find($id);
    }

    /**
     * Update role.
     */
    public function updateRole($id, array $data)
    {
        $role = Role::find($id);

        if (!$role) {
            return null;
        }

        $role->update([
            'name' => $data['name'] ?? $role->name,
            'display_name' => $data['display_name'] ?? $role->display_name,
        ]);

        return $role->fresh('permissions');
    }

    /**
     * Delete role.
     */
    public function deleteRole($id)
    {
        $role = Role::find($id);

        if (!$role) {
            return false;
        }

        DB::transaction(function () use ($role) {

            // Remove permissions assigned to the role
            $role->syncPermissions([]);

            // Remove role from all users
            DB::table('model_has_roles')
                ->where('role_id', $role->id)
                ->delete();

            // Delete role
            $role->delete();
        });

        return true;
    }

    /**
     * Assign permission to role.
     */
    public function assignPermission($roleId, $permissionId)
    {
        $role = Role::find($roleId);
        $permission = Permission::find($permissionId);

        if (!$role || !$permission) {
            return null;
        }

        // Role and permission must use the same guard
        if ($role->guard_name !== $permission->guard_name) {
            return null;
        }

        $role->givePermissionTo($permission);

        return $role->fresh('permissions');
    }

    /**
     * Remove permission from role.
     */
    public function removePermission($roleId, $permissionId)
    {
        $role = Role::find($roleId);
        $permission = Permission::find($permissionId);

        if (!$role || !$permission) {
            return null;
        }

        if ($role->guard_name !== $permission->guard_name) {
            return null;
        }

        $role->revokePermissionTo($permission);

        return $role->fresh('permissions');
    }

    /**
     * Get all permissions.
     */
    public function getAllPermissions()
    {
        return Permission::orderBy('name')->get();
    }

    /**
     * Get permission by ID.
     */
    public function getPermissionById($id)
    {
        return Permission::find($id);
    }

    /**
     * Create permission.
     */
    public function createPermission(array $data)
    {
        return Permission::create([
            'name' => $data['name'],
            'guard_name' => 'sanctum',
        ]);
    }

    /**
     * Update permission.
     */
    public function updatePermission($id, array $data)
    {
        $permission = Permission::find($id);

        if (!$permission) {
            return null;
        }

        $permission->update([
            'name' => $data['name'] ?? $permission->name,
        ]);

        return $permission->fresh();
    }

    /**
     * Delete permission.
     */
    public function deletePermission($id)
    {
        $permission = Permission::find($id);

        if (!$permission) {
            return false;
        }

        DB::transaction(function () use ($permission) {

            // Remove permission from all roles
            $permission->syncRoles([]);

            // Remove direct permission assignments from users
            DB::table('model_has_permissions')
                ->where('permission_id', $permission->id)
                ->delete();

            // Delete permission
            $permission->delete();
        });

        return true;
    }
}

