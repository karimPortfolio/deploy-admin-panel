<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Notifications\AccountActivationNotification;
use App\Notifications\AccountDeactivationNotification;
use App\Notifications\NewUserNotification;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
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
                'role' => $request->input('role', UserRole::USER),
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

     public function getRoles(): array
    {
        $roles = UserRole::cases();
        $roles = array_map(fn($role) => $role->toArray(), $roles);

        return $roles;
    }
}

