<?php

use App\Http\Controllers\Api\V1\SecurityGroupController;
use App\Http\Controllers\Api\V1\ServerController;
use App\Http\Controllers\Api\V1\SshKeyController;
use App\Http\Controllers\Api\V1\VpcController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')
->prefix('v1')
->name('api.v1.')
->group(function () {
    Route::apiResource('ssh-keys', SshKeyController::class);
    Route::apiResource('servers', ServerController::class);
    Route::apiResource('security-groups', SecurityGroupController::class)->except(['update']);
    Route::get('vpcs', VpcController::class)->name('vpcs');
});
