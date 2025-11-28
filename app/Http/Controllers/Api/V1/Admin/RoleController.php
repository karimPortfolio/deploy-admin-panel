<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct(
        private \App\Services\RoleService $roleService
    ) {}


    public function index(Request $request)
    {
        return RoleResource::collection(
            $this->roleService->listRoles($request)
        );
    }

    public function show(Role $role)
    {
        return RoleResource::make(
            $this->roleService->getRole($role)
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
        ]);

        return RoleResource::make(
            $this->roleService->create($request)
        );
    }

    public function update(Role $role, Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name,',
        ]);

        return RoleResource::make(
            $this->roleService->updateRole($role, $request)
        );
    }

    public function assignPermissions(Role $role, Request $request)
    {
        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        return RoleResource::make(
            $this->roleService->assignPermissions($role, $request)
        );
    }

    public function destroy(Role $role)
    {
        $this->roleService->delete($role);

        return response()->noContent();
    }

    public function getPermissions(Role $role)
    {
        return response()->json([
            'data' => $this->roleService->getPermissions($role),
        ]);
    }
}
