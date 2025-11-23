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
    public function __construct(
        private \App\Services\ServerService $serverService
    ) {}


    public function index(Request $request)
    {
        return ServerResource::collection(
            $this->serverService->listServers($request)
        );
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
        
        $notifiable = \App\Models\User::find($server->created_by);
        $lang = $notifiable->language ?? 'en';
        
        Notification::locale($lang)
        ->send($notifiable, new ResourceDeletedNotification($server, 'server', 'servers'));

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
