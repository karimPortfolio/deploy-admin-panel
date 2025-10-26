<?php

use App\Http\Controllers\Api\V1\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Api\V1\Admin\SecurityGroupController as AdminSecurityGroupController;
use App\Http\Controllers\Api\V1\Admin\ServerController as AdminServerController;
use App\Http\Controllers\Api\V1\Admin\SshKeyController as AdminSshKeyController;
use App\Http\Controllers\Api\V1\Admin\UserController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\RdsDatabaseController;
use App\Http\Controllers\Api\V1\SecurityGroupController;
use App\Http\Controllers\Api\V1\ServerController;
use App\Http\Controllers\Api\V1\SshKeyController;
use App\Http\Controllers\Api\V1\UserPreferenceController;
use App\Http\Controllers\Api\V1\VpcController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\NewPasswordController;

Route::middleware(['auth:sanctum', 'setUserLocale', 'isActive'])
    ->prefix('v1')
    ->name('api.v1.')
    ->group(function () {
        Route::get('me', [AuthController::class, 'me'])->name('me');

        // =============== NOTIFCATIONS
        Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::get('notifications/unreceived-notifications', [NotificationController::class, 'unreceivedNotifications'])->name('notifications.unreceived-notifications');
        Route::put('notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
        Route::put('notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');
        Route::delete('notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

        // =============== USER PREFERENCES ROUTES
        Route::put('user/preferences/{user_preference}', [UserPreferenceController::class, 'updatePreferences'])->name('user.preferences.update');

        // =============== VPCS ROUTES
        Route::get('vpcs', VpcController::class)->name('vpcs');

        // =============== USER ROUTES =============
        Route::middleware('role:user')
            ->group(function () {
                // =============== DASHBOARD ROUTES
                Route::prefix('dashboard')
                ->name('dashboard.')
                ->group(function () {
                    Route::get('servers-count', [DashboardController::class, 'getTotalServersCount'])->name('total-servers');
                    Route::get('security-groups-count', [DashboardController::class, 'getTotalSecurityGroupsCount'])->name('total-security-groups');
                    Route::get('sshkeys-count', [DashboardController::class, 'getTotalSshKeysCount'])->name('total-sshkeys');
                    Route::get('monthly-servers-total', [DashboardController::class, 'getMonthlyServersTotal'])->name('monthly-servers-total');
                    Route::get('monthly-security-groups-total', [DashboardController::class, 'getMonthlySecurityGroupsTotal'])->name('monthly-security-groups-total');
                    Route::get('servers-by-security-groups', [DashboardController::class, 'getTotalServersBySecurityGroups'])->name('servers-by-security-groups');
                    Route::get('servers-by-status', [DashboardController::class, 'getTotalServersByStatus'])->name('servers-by-status');
                });

                // =============== SSH KEYS ROUTES
                Route::apiResource('ssh-keys', SshKeyController::class);

                // =============== SERVERS ROUTES
                Route::prefix('servers')
                ->name('servers.')
                ->group(function () {
                    Route::put('{server}/start', [ServerController::class, 'startServer'])->name('start');
                    Route::put('{server}/stop', [ServerController::class, 'stopServer'])->name('stop');
                    Route::get('instance-types', [ServerController::class, 'getInstanceTypes'])->name('instance-types');
                    Route::get('os-families', [ServerController::class, 'getOsFamilies'])->name('os-families');
                    Route::get('statuses', [ServerController::class, 'getServerStatuses'])->name('statuses');
                });
                Route::apiResource('servers', ServerController::class);

                // =============== SECURITY GROUPS ROUTES
                Route::apiResource('security-groups', SecurityGroupController::class)->except(['update']);

                // =============== RDS DATABASES ROUTES
                Route::prefix('rds-databases')
                ->name('rds-databases.')
                ->group(function () {
                    Route::get('statuses', [RdsDatabaseController::class, 'getDatabaseStatuses'])->name('statuses');
                    Route::get('engines', [RdsDatabaseController::class, 'getDatabaseEngines'])->name('engines');
                    Route::get('storage-types', [RdsDatabaseController::class, 'getDatabaseStorageTypes'])->name('storage-types');
                    Route::get('instance-classes', [RdsDatabaseController::class, 'getDatabaseInstanceClasses'])->name('instance-classes');
                    Route::get('servers', [RdsDatabaseController::class, 'getServers'])->name('servers');
                    Route::post('attach', [RdsDatabaseController::class, 'attachDatabaseToServer'])->name('attach');
                });
                Route::apiResource('rds-databases', RdsDatabaseController::class)->except(['update']);
            });

        // =============== ADMIN ROUTES =============
        Route::middleware('role:admin')
            ->prefix('admin')
            ->name('admin.')
            ->group(function () {
                // =============== USERS ROUTES
                Route::put('users/{user}/deactivate', [UserController::class, 'deactivateUserAccount'])->name('users.deactivate');
                Route::put('users/{user}/activate', [UserController::class, 'activateUserAccount'])->name(name: 'users.activate');
                Route::get('users/roles', [UserController::class, 'getRoles'])->name('users.roles');
                Route::apiResource('users', UserController::class)->only(['index', 'store', 'show', 'destroy']);
                // =============== SERVERS ROUTES
                Route::prefix('servers')
                ->name('servers.')
                ->group(function () {
                    Route::put('{server}/start', [AdminServerController::class, 'startServer'])->name('start');
                    Route::put('{server}/stop', [AdminServerController::class, 'stopServer'])->name('stop');
                    Route::get('instance-types', [AdminServerController::class, 'getInstanceTypes'])->name('instance-types');
                    Route::get('os-families', [AdminServerController::class, 'getOsFamilies'])->name('os-families');
                    Route::get('statuses', [AdminServerController::class, 'getServerStatuses'])->name('statuses');
                });
                Route::apiResource('servers', AdminServerController::class)->only(['index', 'show', 'destroy']);
                
                // =============== SECURITY GROUPS ROUTES
                Route::apiResource('security-groups', AdminSecurityGroupController::class)->only(['index', 'show', 'destroy']);
                // =============== SSH KEYS ROUTES
                Route::apiResource('ssh-keys', AdminSshKeyController::class)->only(['index', 'show', 'destroy']);
               
                // =============== DASHBOARD ROUTES
                Route::prefix('dashboard')
                ->name('dashboard.')
                ->group(function () {
                    Route::get('users-count', [AdminDashboardController::class, 'getTotalUsersCount'])->name('total-users');
                    Route::get('servers-count', [AdminDashboardController::class, 'getTotalServersCount'])->name('total-servers');
                    Route::get('security-groups-count', [AdminDashboardController::class, 'getTotalSecurityGroupsCount'])->name('total-security-groups');
                    Route::get('sshkeys-count', [AdminDashboardController::class, 'getTotalSshKeysCount'])->name('total-sshkeys');
                    Route::get('monthly-servers-total', [AdminDashboardController::class, 'getMonthlyServersTotal'])->name('monthly-servers-total');
                    Route::get('monthly-security-groups-total', [AdminDashboardController::class, 'getMonthlySecurityGroupsTotal'])->name('monthly-security-groups-total');
                    Route::get('servers-by-security-groups', [AdminDashboardController::class, 'getTotalServersBySecurityGroups'])->name('servers-by-security-groups');
                    Route::get('servers-by-status', [AdminDashboardController::class, 'getTotalServersByStatus'])->name('servers-by-status');
                });
            });
    });

Route::post('v1/reset-password', [NewPasswordController::class, 'store'])
    ->name('password.reset')
    ->middleware('setUserLocale');
