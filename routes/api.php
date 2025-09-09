<?php

use App\Http\Controllers\Api\V1\Admin\UserController;
use App\Http\Controllers\Api\V1\AmiController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\NotificationsController;
use App\Http\Controllers\Api\V1\SecurityGroupController;
use App\Http\Controllers\Api\V1\ServerController;
use App\Http\Controllers\Api\V1\SshKeyController;
use App\Http\Controllers\Api\V1\UserPreferenceController;
use App\Http\Controllers\Api\V1\VpcController;
use Aws\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\NewPasswordController;
use \App\Http\Controllers\Api\V1\Admin\ServerController as AdminServerController;
use \App\Http\Controllers\Api\V1\Admin\SecurityGroupController as AdminSecurityGroupController;
use \App\Http\Controllers\Api\V1\Admin\SshKeyController as AdminSshKeyController;


Route::middleware('auth:sanctum')
    ->prefix('v1')
    ->name('api.v1.')
    ->group(function () {
        Route::get('me', [AuthController::class, 'me'])->name('me');

        // =============== NOTIFCATIONS
        Route::get('notifications', [NotificationsController::class, 'index'])->name('notifications.index');
        Route::get('notifications/unreceived-notifications', [NotificationsController::class, 'unreceivedNotifications'])->name('notifications.unreceived-notifications');
        Route::put('notifications/{id}/mark-as-read', [NotificationsController::class, 'markAsRead'])->name('notifications.mark-as-read');
        Route::put('notifications/mark-all-as-read', [NotificationsController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');
        Route::delete('notifications/{id}', [NotificationsController::class, 'destroy'])->name('notifications.destroy');

        // =============== USER PREFERENCES ROUTES
        Route::put('user/preferences/{user_preference}', [UserPreferenceController::class, 'updatePreferences'])->name('user.preferences.update');

        // =============== VPCS ROUTES
        Route::get('vpcs', VpcController::class)->name('vpcs');

        // =============== USER ROUTES =============
        Route::middleware('role:user')
            ->group(function () {
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
        });

        // =============== ADMIN ROUTES =============
        Route::middleware('role:admin')
            ->prefix('admin')
            ->group(function () {
            // =============== USERS ROUTES
            Route::put('users/{user}/deactivate', [UserController::class, 'deactivateUserAccount'])->name('users.deactivate');
            Route::put('users/{user}/activate', [UserController::class, 'activateUserAccount'])->name(name: 'users.activate');
            Route::get('users/roles', [UserController::class, 'getRoles'])->name('users.roles');
            Route::apiResource('users', UserController::class)->only(['index', 'show', 'destroy']);
            // =============== SERVERS ROUTES
            Route::put('servers/{server}/start', [AdminServerController::class, 'startServer'])->name('admin.servers.start');
            Route::put('servers/{server}/stop', [AdminServerController::class, 'stopServer'])->name('admin.servers.stop');
            Route::get('servers/instance-types', [AdminServerController::class, 'getInstanceTypes'])->name('servers.instance-types');
            Route::get('servers/os-families', [AdminServerController::class, 'getOsFamilies'])->name('servers.os-families');
            Route::get('servers/statuses', [AdminServerController::class, 'getServerStatuses'])->name('servers.statuses');
            Route::apiResource('servers', AdminServerController::class)->only(['index', 'show', 'destroy']);
            // =============== SECURITY GROUPS ROUTES
            Route::apiResource('security-groups', AdminSecurityGroupController::class)->only(['index', 'show', 'destroy']);
            // =============== SSH KEYS ROUTES
            Route::apiResource('ssh-keys', AdminSshKeyController::class)->only(['index', 'show', 'destroy']);
        });
    });

Route::post('v1/reset-password', [NewPasswordController::class, 'store'])
    ->name('password.reset');