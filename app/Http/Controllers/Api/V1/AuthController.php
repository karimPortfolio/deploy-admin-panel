<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private \App\Services\AuthService $authService
    ) {}
    public function me(Request $request)
    {   
        return UserResource::make($this->authService->authUser());
    }
}
