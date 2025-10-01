<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Enums\ServerStatus;
use App\Http\Controllers\Controller;
use App\Models\SecurityGroup;
use App\Models\Server;
use App\Models\SshKey;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    public function getTotalUsersCount()
    {
        $usersCount = User::query()->count();

        return response()->json([
            'data' => [
                'total' => $usersCount
            ]
        ]);
    }

    public function getTotalServersCount()
    {
        $serversCount = Server::query()->count();

        return response()->json([
            'data' => [
                'total' => $serversCount
            ]
        ]);
    }

    public function getTotalSecurityGroupsCount()
    {
        $securityGroupsCount = SecurityGroup::query()->count();

        return response()->json([
            'data' => [
                'total' => $securityGroupsCount
            ]
        ]);
    }

    public function getTotalSshKeysCount()
    {
        $sshKeysCount = SshKey::query()->count();

        return response()->json([
            'data' => [
                'total' => $sshKeysCount
            ]
        ]);
    }

    public function getMonthlyServersTotal(Request $request)
    {
        $year = $request->input('filter.year') ?? date('Y');
        $servers = Server::query()
            ->select(\DB::raw($this->monthQuery()), \DB::raw("COUNT(*) as count"))
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        $data = [];

        for ($i = 1; $i <= 12; $i++) {
            $data[] = [
                'month' => Carbon::createFromFormat('m', $i)->format('M'),
                'total' => $servers->get($i)->count ?? 0,
            ];
        }

        return response()->json([
            'data' => $data
        ]);
    }


    public function getMonthlySecurityGroupsTotal(Request $request)
    {
        $year = $request->input('filter.year') ?? date('Y');
        $securityGroups = SecurityGroup::query()
            ->select(\DB::raw($this->monthQuery()), \DB::raw("COUNT(*) as count"))
            ->whereYear("created_at", $year)
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        $data = [];

        for ($i = 1; $i <= 12; $i++) {
            $data[] = [
                'month' => Carbon::createFromFormat('m', $i)->format('M'),
                'total' => $securityGroups->get($i)->count ?? 0,
            ];
        }

        return response()->json([
            'data' => $data
        ]);
    }


    public function getTotalServersBySecurityGroups()
    {
        $servers = Server::query()
            ->select('security_group_id', \DB::raw('COUNT(*) as total'))
            ->with('securityGroup')
            ->groupBy('security_group_id')
            ->get()
            ->map(function ($server) {
                return [
                    'securityGroup' => $server->securityGroup->group_id,
                    'total' => $server->total,
                ];
            });


        return response()->json([
            'data' => $servers
        ]);
    }


    public function getTotalServersByStatus()
    {
        $servers = Server::query()
            ->select('status', \DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->get()
            ->map(function ($server) {
                return [
                    'status' => $server->status->label(),
                    'color' => $server->status->hexColor(),
                    'total' => $server->total,
                ];
            });


        return response()->json([
            'data' => $servers
        ]);
    }

    private function monthQuery(): string
    {
        return match (config('database.default')) {
            'mysql', 'pgsql' => 'MONTH(created_at) as month', 
            'sqlite' => "CAST(strftime('%m', created_at) AS INTEGER) as month",
            'sqlsrv' => 'MONTH(created_at) as month',
            default => throw new \Exception('Unsupported database driver: ' . config('database.default')),
        };
    }
}
