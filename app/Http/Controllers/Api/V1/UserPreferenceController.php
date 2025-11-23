<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserPreferenceRequest;
use App\Models\UserPreference;

class UserPreferenceController extends Controller
{
    public function __construct(
        private \App\Services\UserPreferenceService $userPreferenceService
    ){}

    public function updatePreferences(UserPreferenceRequest $request, UserPreference $userPreference)
    {
        $this->userPreferenceService->updateUserPreferences($request, $userPreference);
        
        return response()->noContent();
    }
}
