<?php

namespace App\Services;

class AuthService
{
    public function authUser()
    {
        $user = auth()->user();
        $user->load('preferences');
        $user->loadMedia('profile-picture');
        $user->getFirstMedia('profile-picture');

        return $user;
    }
}

