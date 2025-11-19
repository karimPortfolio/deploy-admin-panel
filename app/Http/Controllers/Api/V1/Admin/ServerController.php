<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Enums\InstanceType;
use App\Enums\OsFamily;
use App\Enums\ServerStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\ServerResource;
use App\Models\Server;
use App\Notifications\ResourceDeletedNotification;
use App\Services\Ec2InstanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ServerController extends Controller
{
    public function index(Request $request)
    {
        $servers = QueryBuilder::for(Server::class)
            ->allowedFilters([
                AllowedFilter::exact('status'),
                AllowedFilter::exact('security_group_id', 'securityGroup.group_id'),
                AllowedFilter::exact('ssh_key_id'),
                AllowedFilter::exact('instance_type'),
                AllowedFilter::exact('os_family'),
                AllowedFilter::exact('vpc_id'),
                AllowedFilter::custom('created_at', new \App\Filters\DateFilter()),
            ])
            ->allowedSorts(['id', 'name', 'instance_id', 'public_ip_address', 'status', 'created_at'])
            ->with([
                'sshKey:id,name',
                'securityGroup:id,name,group_id',
                'createdBy:id,name',
            ])
            ->when($request->input('search'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->input('search') . '%')
                    ->orWhere('instance_id', 'like', '%' . $request->input('search') . '%');
                $query->orWhere('status', 'like', '%' . $request->input('search') . '%');
                $query->orWhere('instance_type', 'like', '%' . $request->input('search') . '%');
                $query->orWhere('vpc_id', 'like', '%' . $request->input('search') . '%');
                $query->orWhereHas('sshKey', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->input('search') . '%');
                });
                $query->orWhere('public_ip_address', 'like', '%' . $request->input('search') . '%');
                $query->orWhereHas('securityGroup', function ($q) use ($request) {
                    $q->where('group_id', 'like', '%' . $request->input('search') . '%');
                    $q->orWhere('name', 'like', '%' . $request->input('search') . '%');
                });
            })
            ->orderBy('updated_at', 'desc')
            ->paginate(function ($total) use ($request) {
                $perPage = $request->integer('per_page', -1);

                if ($perPage === 0) {
                    $perPage = $total;
                } elseif ($perPage === -1) {
                    $perPage = 5;
                }

                return $perPage;
            });

        return ServerResource::collection($servers);
    }

    public function show(Server $server)
    {
        $server->load([
            'sshKey:id,name',
            'securityGroup:id,name,group_id',
            'createdBy:id,name',
        ]);

        $statistics = Ec2InstanceService::getInstanceUtilization($server->instance_id);
        
        return (new ServerResource($server))
        ->additional([
            'statistics' => $statistics,
        ]);
    }

    public function startServer(Server $server)
    {
        $result = Ec2InstanceService::startInstance($server->instance_id);

        if ($result['StartingInstances'][0]['CurrentState']['Name'] !== 'pending') {
            return response()->json(['message' => __('messages.servers.failed_to_start_msg')], 500);
        }

        $server->update([
            'status' => 'running',
        ]);

        return response()->noContent();
    }

    public function stopServer(Server $server)
    {
        $result = Ec2InstanceService::stopInstance($server->instance_id);

        if ($result['StoppingInstances'][0]['CurrentState']['Name'] !== 'stopping') {
            return response()->json(['message' => __('messages.servers.failed_to_stop_msg')], 500);
        }

        $server->update([
            'status' => 'stopped',
        ]);

        return response()->noContent();
    }

    public function destroy(Server $server)
    {

        $result = Ec2InstanceService::terminateInstance($server->instance_id);

        if ($result['TerminatingInstances'][0]['CurrentState']['Name'] !== 'shutting-down') {
            return response()->json(['message' => __('messages.servers.failed_to_terminate_msg')], 500);
        }

        $server->delete();
        
        $notifiable = \App\Models\User::find($server->created_by);
        $lang = $notifiable->language ?? 'en';
        
        Notification::locale($lang)
        ->send($notifiable, new ResourceDeletedNotification($server, 'server', 'servers'));

        return response()->noContent();
    }

    public function getInstanceTypes()
    {
        $instanceTypes = InstanceType::cases();

        $instanceTypes = array_map(function ($type) {
            return $type->toArray();
        }, $instanceTypes);

        return response()->json([
            'data' => $instanceTypes,
        ]);
    }


    public function getOsFamilies()
    {
        $osFamilies = OsFamily::cases();

        $osFamilies = array_map(fn($family) => $family->toArray(), $osFamilies);

        return response()->json([
            'data' => $osFamilies,
        ]);
    }

    public function getServerStatuses()
    {
        $statuses = ServerStatus::cases();
        $statuses = array_map(fn($status) => $status->toArray(), $statuses);

        return response()->json([
            'data' => $statuses,
        ]);
    }
}
