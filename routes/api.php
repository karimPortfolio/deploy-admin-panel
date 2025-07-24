<?php

use App\Http\Controllers\Api\V1\AmiController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DashboardController;
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

        // =============== DASHBOARD ROUTES
        Route::get('/dashboard/servers-count', [DashboardController::class, 'getTotalServersCount'])->name('dashboard.total-servers');
        Route::get('/dashboard/security-groups-count', [DashboardController::class, 'getTotalSecurityGroupsCount'])->name('dashboard.total-security-groups');
        Route::get('/dashboard/sshkeys-count', [DashboardController::class, 'getTotalSshKeysCount'])->name('dashboard.total-sshkeys');
        Route::get('/dashboard/monthly-servers-total', [DashboardController::class, 'getMonthlyServersTotal'])->name('dashboard.monthly-servers-total');
        Route::get('/dashboard/monthly-security-groups-total', [DashboardController::class, 'getMonthlySecurityGroupsTotal'])->name('dashboard.monthly-security-groups-total');
        Route::get('/dashboard/servers-by-security-groups', [DashboardController::class, 'getTotalServersBySecurityGroups'])->name('dashboard.servers-by-security-groups');
        Route::get('/dashboard/servers-by-status', [DashboardController::class, 'getTotalServersByStatus'])->name('dashboard.servers-by-status');
        
        // =============== SSH KEYS ROUTES
        Route::apiResource('ssh-keys', SshKeyController::class);
        
        // =============== SERVERS ROUTES
        Route::put('servers/{server}/start', [ServerController::class, 'startServer'])->name('servers.start');
        Route::put('servers/{server}/stop', [ServerController::class, 'stopServer'])->name('servers.stop');
        Route::get('servers/instance-types', [ServerController::class, 'getInstanceTypes'])->name('servers.instance-types');
        Route::get('servers/os-families', [ServerController::class, 'getOsFamilies'])->name('servers.os-families');
        Route::get('servers/statuses', [ServerController::class, 'getServerStatuses'])->name('servers.statuses');
        Route::apiResource('servers', ServerController::class);
        
        // =============== SECURITY GROUPS ROUTES
        Route::apiResource('security-groups', SecurityGroupController::class)->except(['update']);
        
        // =============== VPCS ROUTES
        Route::get('vpcs', VpcController::class)->name('vpcs');
    });

Route::post('v1/reset-password', [NewPasswordController::class, 'store'])
    ->name('password.reset');