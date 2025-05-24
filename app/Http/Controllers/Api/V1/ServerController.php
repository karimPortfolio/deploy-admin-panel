<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\InstanceType;
use App\Enums\OsFamily;
use App\Http\Controllers\Controller;
use App\Http\Requests\ServerRequest;
use App\Http\Resources\ServerResource;
use App\Jobs\CreateEc2InstanceJob;
use App\Models\SecurityGroup;
use App\Models\Server;
use App\Services\Ec2InstanceService;
use Illuminate\Http\Request;

class ServerController extends Controller
{
    public function index()
    {
        $servers = Server::query()
        ->with([
            'sshKey:id,name',
            'securityGroup:id,name',
        ])
        ->orderBy('updated_at')
        ->get();

        return ServerResource::collection($servers);
    }



    public function store(ServerRequest $request)
    {
        $securityGroup = SecurityGroup::findOrFail($request->validated('security_group_id'));

        $params = [
            'os_family' => OsFamily::tryFrom($request->validated('os_family')),
            'instance_type' => InstanceType::tryFrom($request->validated('instance_type')),
            'name' => $request->validated('name'),
            'vpc_id' => $request->validated('vpc_id'),
            'group_id' => $securityGroup->group_id,
        ];

        $server = Server::create([
            'name' => $params['name'],
            'instance_type' => $params['instance_type']->value,
            'security_group_id' => $securityGroup->id,
        ]);

        //running the EC2 creation job in queue
        CreateEc2InstanceJob::dispatch($server, $params);
        
        return (new ServerResource($server))
            ->additional([
                'message' => 'Server creation in progress. You can check the status later.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    public function show(Server $server)
    {
        $server->load([
            'sshKey:id,name',
            'securityGroup:id,name',
        ]);

        return new ServerResource($server);
    }

    public function startServer(Server $server)
    {
        $result = Ec2InstanceService::startInstance($server->instance_id);

        if ($result['StartingInstances'][0]['CurrentState']['Name'] !== 'pending') {
            return response()->json(['message' => 'Failed to start the instance.'], 500);
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
            return response()->json(['message' => 'Failed to stop the instance.'], 500);
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
            return response()->json(['message' => 'Failed to terminate the instance.'], 500);
        }

        $server->delete();

        return response()->noContent();
    }
}
