<?php

namespace App\Services;

use App\Enums\InstanceType;
use App\Enums\OsFamily;
use App\Enums\ServerStatus;
use App\Http\Requests\RdsDatabaseSnapshotRequest;
use App\Http\Requests\ServerRequest;
use App\Jobs\CreateEc2InstanceJob;
use App\Jobs\CreateRdsDatabaseSnapshotJob;
use App\Models\RdsDatabaseSnapshot;
use App\Models\SecurityGroup;
use App\Models\Server;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ServerService
{
    public function __construct(
        private \App\Services\AWS\Ec2InstanceService $awsEc2InstanceService
    ){}

    public function listServers(Request $request): mixed
    {
        $search = $request->input('search');
        return QueryBuilder::for(Server::class)
            ->allowedFilters(
                $this->getFiltersArray()
            )
            ->allowedSorts(['id', 'name', 'instance_id', 'public_ip_address', 'status', 'created_at'])
            ->with([
                'sshKey:id,name',
                'securityGroup:id,name,group_id',
                'createdBy:id,name',
            ])
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('instance_id', 'like', '%' . $search . '%');
                $query->orWhere('status', 'like', '%' . $search . '%');
                $query->orWhere('instance_type', 'like', '%' . $search . '%');
                $query->orWhere('vpc_id', 'like', '%' . $search . '%');
                $query->orWhereHas('sshKey', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
                $query->orWhere('public_ip_address', 'like', '%' . $search . '%');
                $query->orWhereHas('securityGroup', function ($q) use ($search) {
                    $q->where('group_id', 'like', '%' . $search . '%');
                    $q->orWhere('name', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('updated_at', 'desc')
            ->paginate($request->input('per_page', 15));
    }

     public function create(Request $request): Server
    {
        $securityGroup = SecurityGroup::findOrFail($request->validated('security_group_id'));

        $params = [
            'os_family' => OsFamily::tryFrom($request->validated('os_family')),
            'instance_type' => InstanceType::tryFrom($request->validated('instance_type')),
            'name' => $request->validated('name'),
            'vpc_id' => $request->validated('vpc_id'),
            'group_id' => $securityGroup->group_id,
            'created_by' => auth()->id()
        ];

        $server = Server::create([
            'name' => $params['name'],
            'instance_type' => $params['instance_type']->value,
            'security_group_id' => $securityGroup->id,
            'os_family' => $params['os_family']->value,
            'status' => ServerStatus::PENDING,
            'created_by' => auth()->id()
        ]);

        //running the EC2 creation job in queue
        CreateEc2InstanceJob::dispatch($server, $params);

        return $server;
    }

    public function getServer(Server $server): array
    {
        $server->load([
            'sshKey:id,name',
            'securityGroup:id,name,group_id',
            'createdBy:id,name',
            'rdsDatabases:id,db_instance_identifier,db_name,engine',
        ]);

        $statistics = $this->awsEc2InstanceService->getInstanceUtilization($server->instance_id);

        return [
            'server' => $server,
            'statistics' => $statistics,
        ];
    }

    public function startServer(Server $server): Server
    {
        $result =  $this->awsEc2InstanceService->startInstance($server->instance_id);

        if ($result['StartingInstances'][0]['CurrentState']['Name'] !== 'pending') {
            throw new \Exception(__('messages.servers.failed_to_start_msg'));
        }

        $server->update([
            'status' => 'running',
        ]);

        return $server;
    }

    public function stopServer(Server $server): Server
    {
        $result = $this->awsEc2InstanceService->stopInstance($server->instance_id);

        if ($result['StoppingInstances'][0]['CurrentState']['Name'] !== 'stopping') {
            throw new \Exception(__('messages.servers.failed_to_stop_msg'));
        }

        $server->update([
            'status' => 'stopped',
        ]);

        return $server;
    }

    public function delete(Server $server)
    {
        $result = $this->awsEc2InstanceService->terminateInstance($server->instance_id);

        if ($result['TerminatingInstances'][0]['CurrentState']['Name'] !== 'shutting-down') {
            throw new \Exception(__('messages.servers.failed_to_terminate_msg'));
        }

        $server->delete();
    }

     public function getInstanceTypes(): array
    {
        $instanceTypes = InstanceType::cases();

        $instanceTypes = array_map(function ($type) {
            return $type->toArray();
        }, $instanceTypes);

        return $instanceTypes;
    }

    public function getOsFamilies(): array
    {
        $osFamilies = OsFamily::cases();

        $osFamilies = array_map(fn($family) => $family->toArray(), $osFamilies);

        return $osFamilies;
    }

    public function getServerStatuses(): array
    {
        $statuses = ServerStatus::cases();
        $statuses = array_map(fn($status) => $status->toArray(), $statuses);

        return $statuses;
    }

    private function getFiltersArray(): array
    {
        $filtersArray = [
                AllowedFilter::exact('status'),
                AllowedFilter::exact('security_group_id', 'securityGroup.group_id'),
                AllowedFilter::exact('ssh_key_id'),
                AllowedFilter::exact('instance_type'),
                AllowedFilter::exact('os_family'),
                AllowedFilter::exact('vpc_id'),
                AllowedFilter::custom('created_at', new \App\Filters\DateFilter()),
        ];

        if (auth()->user()->isAdmin())
        {
            array_push($filtersArray, AllowedFilter::exact('created_by'));
        }

        return $filtersArray;
    }
}

