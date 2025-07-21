<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function me(Request $request)
    {
        $user = auth()->user();

        $user->loadMedia("profile-picture");
        $user->getFirstMedia("profile-picture");
        
        return UserResource::make($user);
    }
}
