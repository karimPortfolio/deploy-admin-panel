<?php

namespace App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\QueryBuilder\QueryBuilder;

class RoleService
{

    public function listRoles(Request $request): LengthAwarePaginator
    {
        $search = $request->input('search');

        return QueryBuilder::for(Role::class)
            ->allowedFilters([
                'created_at',
            ])
            ->allowedSorts(['name', 'created_at', 'id'])
            ->withCount('permissions')
            ->when($search, function ($query) use ($request, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('updated_at', 'desc')
            ->paginate($request->input('per_page', 15));

    }

    public function getRole(Role $role): Role
    {
        return $role->load('permissions');
    }

    public function create(Request $request)
    {
        return Role::create([
            'name' => $request->input('name'),
            'guard_name' => 'web',
        ]);
    }

    public function updateRole(Role $role, Request $request): Role
    {
        $role->update([
            'name' => $request->input('name', $role->name),
        ]);

        return $role;
    }

    public function assignPermissions(Role $role, Request $request): Role
    {
        \DB::transaction(function () use ($role, $request) {
            $role->syncPermissions($request->input('permissions', []));
        });

        return $role;
    }

    public function delete(Role $role): void
    {
        $role->delete();
    }

    public function getPermissions(Role $role): array
    {
        $groups = [
            'Servers' => Permission::where('name', 'like', 'servers.%')->get(),
            'Security Groups' => Permission::where('name', 'like', 'security-groups.%')->get(),
            'SSH Keys' => Permission::where('name', 'like', 'ssh-keys.%')->get(),
            'RDS Databases' => Permission::where('name', 'like', 'rds-databases.%')->get(),
            'RDS Database Snapshots' => Permission::where('name', 'like', 'rds-database-snapshots.%')->get(),
        ];

        $labelMap = [
            'index' => 'view',
            'show' => 'view details',
            'create' => 'create',
            'delete' => 'delete',
            'start' => 'start',
            'stop' => 'stop',
            'attach-to-server' => 'attach to server',
            'update-primary' => 'update primary',
            'detach-from-server' => 'detach from server',
            'manage-backups' => 'manage backups',
            '*' => 'all',
        ];

        $response = $this->getAssociatedPermissions($groups, $role, $labelMap);

        return $response;
    }

    private function getAssociatedPermissions(array $groups, Role $role, array $labelMap): array
    {
        $response = [];

        foreach ($groups as $groupName => $perms) {
            $response[$groupName] = $perms->map(function (Permission $p) use ($groupName, $role, $labelMap) {
                $parts = explode('.', $p->name, 2);
                $action = $parts[1] ?? $p->name;

                // Human label: e.g. "create servers"
                $actionLabel = $labelMap[$action] ?? str_replace('-', ' ', $action);
                $label = sprintf('%s %s', $actionLabel, strtolower($groupName));

                return [
                    'id' => $p->id,
                    'name' => $label,
                    'key' => $p->name,
                    'assigned' => $role->hasPermissionTo($p->name),
                ];
            })->values()->all();
        }

        return $response;
    }
}

