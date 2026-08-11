<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Services\RoleService;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    protected RoleService $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * Get all roles
     */
    public function index()
    {
        $roles = $this->roleService->getAllRoles();

        return response()->json([
            'status' => true,
            'data' => $roles
        ]);
    }

    /**
     * Create role
     */
    public function store(StoreRoleRequest $request)
    {
        $role = $this->roleService->createRole(
            $request->validated()
        );

        return response()->json([
            'status' => true,
            'message' => 'Role created successfully',
            'data' => $role
        ], 201);
    }

    /**
     * Get one role
     */
    public function show(string $id)
    {
        $role = $this->roleService->getRoleById($id);

        if (!$role) {
            return response()->json([
                'status' => false,
                'message' => 'Role not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $role
        ]);
    }

    /**
     * Update role
     */
    public function update(UpdateRoleRequest $request, string $id)
    {
        $role = $this->roleService->updateRole(
            $id,
            $request->validated()
        );

        if (!$role) {
            return response()->json([
                'status' => false,
                'message' => 'Role not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Role updated successfully',
            'data' => $role
        ]);
    }

    /**
     * Delete role
     */
    public function destroy(string $id)
    {
        $deleted = $this->roleService->deleteRole($id);

        if (!$deleted) {
            return response()->json([
                'status' => false,
                'message' => 'Role not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Role deleted successfully'
        ]);
    }

    /**
     * Get all permissions
     */
    public function permissions()
    {
        $permissions = $this->roleService->getAllPermissions();

        return response()->json([
            'status' => true,
            'data' => $permissions
        ]);
    }

    /**
     * Assign permission to role
     */
    public function assignPermission(Request $request, string $roleId)
    {
        $request->validate([
            'permission_id' => [
                'required',
                'integer',
                'exists:permissions,id'
            ]
        ]);

        $role = $this->roleService->assignPermission(
            $roleId,
            $request->permission_id
        );

        if (!$role) {
            return response()->json([
                'status' => false,
                'message' => 'Role or permission not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Permission assigned successfully',
            'data' => $role
        ]);
    }

    /**
     * Remove permission from role
     */
    public function removePermission(Request $request, string $roleId)
    {
        $request->validate([
            'permission_id' => [
                'required',
                'integer',
                'exists:permissions,id'
            ]
        ]);

        $role = $this->roleService->removePermission(
            $roleId,
            $request->permission_id
        );

        if (!$role) {
            return response()->json([
                'status' => false,
                'message' => 'Role or permission not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Permission removed successfully',
            'data' => $role
        ]);
    }

    /**
     * Create permission
     */
    public function storePermission(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:permissions,name'
            ],
            'guard_name' => [
                'nullable',
                'string',
                'max:255'
            ]
        ]);

        $permission = $this->roleService->createPermission($validated);

        return response()->json([
            'status' => true,
            'message' => 'Permission created successfully',
            'data' => $permission
        ], 201);
    }
}
