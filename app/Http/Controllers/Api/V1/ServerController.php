<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\InstanceType;
use App\Enums\OsFamily;
use App\Enums\ServerStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\ServerRequest;
use App\Http\Resources\ServerResource;
use App\Jobs\CreateEc2InstanceJob;
use App\Models\SecurityGroup;
use App\Models\Server;
use App\Services\Ec2InstanceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ServerController extends Controller
{
    public function __construct(
        private \App\Services\ServerService $serverService
    ) {}

    public function index(Request $request)
    {
        return ServerResource::collection(
            $this->serverService->listServers($request)
        );
    }

    public function store(ServerRequest $request)
    {
        $server = $this->serverService->create($request);
        
        return (new ServerResource($server))
            ->additional([
                'message' => __('messages.servers.servers_creation_msg'),
            ])
            ->response()
            ->setStatusCode(201);
    }

    public function show(Server $server)
    {
        $serverDetails = $this->serverService->getServer($server);

        return (new ServerResource($serverDetails['server']))
            ->additional([
                'statistics' => $serverDetails['statistics'],
            ]);
    }

    public function startServer(Server $server)
    {
        $this->serverService->startServer($server);

        return response()->noContent();
    }

    public function stopServer(Server $server)
    {
        $this->serverService->stopServer($server);

        return response()->noContent();
    }


    public function destroy(Server $server)
    {
        $this->serverService->delete($server);

        return response()->noContent();
    }


    public function getInstanceTypes()
    {
        return response()->json([
            'data' => $this->serverService->getInstanceTypes(),
        ]);
    }


    public function getOsFamilies()
    {
       return response()->json([
            'data' => $this->serverService->getOsFamilies(),
        ]);
    }

    public function getServerStatuses()
    {
        return response()->json([
            'data' => $this->serverService->getServerStatuses(),
        ]);
    }
}
