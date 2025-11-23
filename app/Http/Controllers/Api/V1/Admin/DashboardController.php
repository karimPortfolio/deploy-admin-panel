<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private DashboardService $dashboardService
    ){}

    public function getTotalUsersCount()
    {
        return response()->json([
            'data' => $this->dashboardService->totalUsersCount(),
        ]);
    }

    public function getTotalServersCount()
    {
        return response()->json([
            'data' => $this->dashboardService->totalServersCount(),
        ]);
    }

    public function getTotalSecurityGroupsCount()
    {
        return response()->json([
            'data' => $this->dashboardService->totalSecurityGroupsCount(),
        ]);
    }

    public function getTotalSshKeysCount()
    {
        return response()->json([
            'data' => $this->dashboardService->totalSshKeysCount(),
        ]);
    }

    public function getTotalRdsDatabasesCount()
    {
        return response()->json([
            'data' => $this->dashboardService->totalRdsDatabasesCount(),
        ]);
    }

    public function getMonthlyServersTotal(Request $request)
    {
        return response()->json([
            'data' => $this->dashboardService->monthlyServersTotal($request),
        ]);
    }

    public function getMonthlySecurityGroupsTotal(Request $request)
    {
        return response()->json([
            'data' => $this->dashboardService->monthlySecurityGroupsTotal($request),
        ]);
    }

    public function getMonthlyRdsDatabasesTotal(Request $request)
    {
        return response()->json([
            'data' => $this->dashboardService->monthlyRdsDatabasesTotal($request),
        ]);
    }

    public function getTotalServersBySecurityGroups()
    {
        return response()->json([
            'data' => $this->dashboardService->totalServersBySecurityGroups(),
        ]);
    }

    public function getTotalServersByStatus()
    {
        return response()->json([
            'data' => $this->dashboardService->totalServersByStatus(),
        ]);
    }

}
