<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServerRequest;
use App\Http\Resources\ServerResource;
use App\Models\Server;
use Illuminate\Http\Request;

class ServerController extends Controller
{
    public function index()
    {
        $servers = Server::query()
        ->with([
            'sshKey:id, name',
            'securityGroup:id, name',
        ])
        ->orderBy('updated_at')
        ->get();

        return ServerResource::collection($servers);
    }



    public function store(ServerRequest $request)
    {
        
    }

    public function show(Server $server)
    {
        $server->load([
            'sshKey:id, name',
            'securityGroup:id, name',
        ]);

        return new ServerResource($server);
    }

    public function update(Request $request, string $id)
    {
        //
    }


    public function destroy(string $id)
    {
        //
    }
}
