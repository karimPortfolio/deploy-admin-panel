<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserPreferenceRequest;
use App\Models\UserPreference;
use Illuminate\Http\Request;

class UserPreferenceController extends Controller
{
    public function updatePreferences(UserPreferenceRequest $request, UserPreference $userPreference)
    {
        $userPreference->update([
            'user_id' => auth()->id(),
            'preferences' => [
                'language' => $request->input('language'),
                'theme' => $request->input('theme'),
                'notification' => [
                    'email' => $request->input('notification.email', true),
                    'system' => $request->input('notification.system', true),
                ],
            ]
        ]);

        return response()->noContent();
    }
}
