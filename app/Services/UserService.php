<?php

namespace App\Services;

use App\Enums\ProfileType;
use App\Http\Requests\UserRequest;
use App\Http\Resources\RoleResource;
use App\Models\User;
use App\Notifications\AccountActivationNotification;
use App\Notifications\AccountDeactivationNotification;
use App\Notifications\NewUserNotification;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Permission;
use Spatie\QueryBuilder\QueryBuilder;

class UserService
{
    public function listUsers(Request $request): LengthAwarePaginator
    {
        return QueryBuilder::for(User::class)
            ->allowedFilters(['name', 'email', 'role'])
            ->allowedSorts(['id', 'name', 'email', 'created_at'])
            ->with('media')
            ->whereNot('id', auth()->id())
            ->when($request->has('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('role', 'like', "%{$search}%")
                        ->orWhere('company_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('updated_at', 'desc')
            ->paginate($request->integer('per_page', 15));
    }

     public function store(UserRequest $request): mixed
    {
        $generatedPassword = User::generateRandomPassword();

        return \DB::transaction(function () use ($request, $generatedPassword) {
            $newUser = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'company_name' => $request->input('company_name', null),
                'role' => $request->input('role', ProfileType::USER),
                'password' => Hash::make($generatedPassword),
                'is_active' => $request->input('is_active', true),
            ]);

            $newUser->preferences()->create([
                'preferences' => [
                    'theme' => 'auto',
                    'language' => 'en',
                    'notification' => ['email' => true, 'system' => true]
                ]
            ]);

            Notification::send($newUser, new NewUserNotification($generatedPassword));

            return $newUser;
        });
    }

    public function getUser(User $user): User
    {
        return $user
                ->loadCount([
                    'servers',
                    'sshKeys',
                    'securityGroups',
                ])
                ->load('media')
                ->append('session');
    }

    public function activateUserAccount(User $user): User
    {
        
        $user->update(['is_active' => true]);

        $lang = $user->language ?? 'en';

        Notification::locale($lang)
                    ->sendNow($user, new AccountActivationNotification());

        return $user;
    }

    public function deactivateUserAccount(User $user): User
    {
        $user->update(['is_active' => false]);

        $lang = $user->language ?? 'en';

        Notification::locale($lang)
        ->sendNow($user, new AccountDeactivationNotification());

        return $user;
    }

    public function assignPermissions(User $user, array $permissions): User
    {
        $user->syncPermissions($permissions);

        return $user;
    }

    public function assignRoles(User $user, array $roles): User
    {
        $user->syncRoles($roles);

        return $user;
    }


    public function delete(User $user): void
    {
        $user->preferences()->delete();
        $user->media()->delete();
        $user->servers()->delete();
        $user->sshKeys()->delete();
        $user->notifications()->delete();
        $user->tokens()->delete();
        $user->securityGroups()->delete();
        $user->delete();
    }

     public function getProfileTypes(): array
    {
        $profileTypes = ProfileType::cases();
        $profileTypes = array_map(fn($role) => $role->toArray(), $profileTypes);

        return $profileTypes;
    }

    public function getRoles(User $user)
    {
        $roles = \Spatie\Permission\Models\Role::all();
        $roles = $this->getAssociatedRoles($user, $roles->toArray());

        return $roles;
    }

    public function getPermissions(User $user): array
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

        $response = $this->getAssociatedPermissions($groups, $user, $labelMap);

        return $response;
    }

    private function getAssociatedPermissions(array $groups, User $user, array $labelMap): array
    {
        $response = [];

        foreach ($groups as $groupName => $perms) {
            $response[$groupName] = $perms->map(function (Permission $p) use ($groupName, $user, $labelMap) {
                $parts = explode('.', $p->name, 2);
                $action = $parts[1] ?? $p->name;

                $actionLabel = $labelMap[$action] ?? str_replace('-', ' ', $action);
                $label = sprintf('%s %s', $actionLabel, strtolower($groupName));

                return [
                    'id' => $p->id,
                    'name' => $label,
                    'key' => $p->name,
                    'assigned' => $user->hasPermissionTo($p->name),
                ];
            })->values()->all();
        }

        return $response;
    }

    private function getAssociatedRoles(User $user, array $permissions): array
    {
        $assignedRoles = $user->roles->pluck('name')->toArray();

        $permissions = array_map(function ($role) use ($assignedRoles) {
            $role['assigned'] = in_array($role['name'], $assignedRoles);
            return $role;
        }, $permissions);

        return $permissions;
    }

}

