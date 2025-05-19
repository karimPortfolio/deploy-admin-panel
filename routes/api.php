<?php

use App\Http\Controllers\Api\V1\ServerController;
use App\Http\Controllers\Api\V1\SshKeyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
->name('api.v1.')
->group(function () {
    Route::apiResource('ssh-keys', SshKeyController::class);
    Route::apiResource('servers', ServerController::class);
});
