<?php

namespace App\Services;

use App\Http\Requests\UserPreferenceRequest;
use App\Models\UserPreference;

class UserPreferenceService
{

    public function updateUserPreferences(UserPreferenceRequest $request, UserPreference $userPreference)
    {
        return $userPreference->update([
            'preferences' => [
                'language' => $request->input('language'),
                'theme' => $request->input('theme'),
                'notification' => [
                    'email' => $request->input('notification.email', true),
                    'system' => $request->input('notification.system', true),
                ],
            ]
        ]);
    }
}

