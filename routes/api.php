<?php

use App\Http\Controllers\Api\V1\AmiController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\SecurityGroupController;
use App\Http\Controllers\Api\V1\ServerController;
use App\Http\Controllers\Api\V1\SshKeyController;
use App\Http\Controllers\Api\V1\VpcController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\NewPasswordController;

Route::middleware('auth:sanctum')
->prefix('v1')
->name('api.v1.')
->group(function () {
    Route::get('me', [AuthController::class, 'me'])->name('me');
    Route::apiResource('ssh-keys', SshKeyController::class);
    Route::put('servers/{server}/start', [ServerController::class, 'startServer'])->name('servers.start');
    Route::put('servers/{server}/stop', [ServerController::class, 'stopServer'])->name('servers.stop');
    Route::get('servers/instance-types', [ServerController::class, 'getInstanceTypes'])->name('servers.instance-types');
    Route::get('servers/os-families', [ServerController::class, 'getOsFamilies'])->name('servers.os-families');
    Route::get('servers/statuses', [ServerController::class, 'getServerStatuses'])->name('servers.statuses');
    Route::apiResource('servers', ServerController::class);
    Route::apiResource('security-groups', SecurityGroupController::class)->except(['update']);
    Route::get('vpcs', VpcController::class)->name('vpcs');
});

Route::post('v1/reset-password', [NewPasswordController::class, 'store'])
    ->name('password.reset');