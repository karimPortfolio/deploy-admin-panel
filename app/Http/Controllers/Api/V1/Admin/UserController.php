<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;


class UserController extends Controller
{
    public function __construct(
        protected \App\Services\UserService $userService
    ) {}


    public function index(Request $request)
    {
        return UserResource::collection(
            $this->userService->listUsers($request)
        );
    }

    public function store(UserRequest $request)
    {
        $this->userService->store($request);

        return response()->noContent();
    }

    public function show(User $user)
    {
        return new UserResource(
            $this->userService->getUser($user)
        );
    }

    public function activateUserAccount(User $user)
    {
        $this->userService->activateUserAccount($user);

        return response()->noContent();
    }

    public function deactivateUserAccount(User $user)
    {
        $this->userService->deactivateUserAccount($user);

        return response()->noContent();
    }

    public function destroy(User $user)
    {
        $this->userService->delete($user);

        return response()->noContent();
    }

    public function getRoles()
    {
        return response()->json([
            'data' => $this->userService->getRoles(),
        ]);
    }

}
