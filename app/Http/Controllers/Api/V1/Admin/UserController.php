<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = QueryBuilder::for(User::class)
            ->allowedFilters(['name', 'email', 'role'])
            ->allowedSorts(['name', 'email', 'created_at'])
            ->orderBy('updated_at', 'desc')
            ->paginate(function ($total) use ($request) {
                $perPage = $request->integer('per_page', -1);

                if ($perPage === 0) {
                    $perPage = $total;
                } elseif ($perPage === -1) {
                    $perPage = 10;
                }

                return $perPage;
            });

        return UserResource::collection($users);
    }

    public function show(User $user)
    {
        return new UserResource($user);
    }

    public function changeUserToInactive(User $user)
    {
        $user->update(['is_active' => false]);

        return response()->noContent();
    }

    public function changeUserToActive(User $user)
    {
        $user->update(['is_active' => true]);

        return response()->noContent();
    }

    public function destroy(User $user)
    {
        $user->preferences()->delete();
        $user->media()->delete();
        $user->servers()->delete();
        $user->sshKeys()->delete();
        $user->notifications()->delete();
        $user->tokens()->delete();
        $user->securityGroups()->delete();
        $user->delete();

        return response()->noContent();
    }
}
