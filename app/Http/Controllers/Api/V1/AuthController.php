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
        
        return UserResource::make($user);
    }
}
