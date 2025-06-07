<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\SshKeyRequest;
use App\Http\Resources\SshKeyResource;
use App\Models\SshKey;
use App\Services\SshKeyService;
use GuzzleHttp\Psr7\Query;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class SshKeyController extends Controller
{

    public function index(Request $request)
    {
        $sshKeys = QueryBuilder::for(SshKey::class)
            ->allowedFilters([
                'name',
                'created_at',
            ])
            ->allowedSorts(['name', 'created_at', 'id'])
            ->withCount('servers')
            ->when($request->input('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('public_key', 'like', "%{$search}%");
            })
            ->orderBy('updated_at', 'desc')
            ->paginate($request->input('per_page', 10));

        return SshKeyResource::collection($sshKeys);
    }


    public function store(SshKeyRequest $request)
    {
        // create the ssh  public and private keys
        $keys = SshKeyService::createSshKey($request->validated('name'));

        // store the ssh key
        $newSshKey = SshKey::create([
            'name' => $request->validated('name'),
            'public_key' => $keys['public_key'],
            'private_key' => $keys['private_key'],
        ]);

        return new SshKeyResource($newSshKey);
    }

    public function show(SshKey $sshKey)
    {
        $sshKey->load('servers:id,instance_id,status,ssh_key_id');

        return new SshKeyResource($sshKey);
    }

    public function update(SshKeyRequest $request, SshKey $sshKey)
    {
        $sshKey->update([
            'name' => $request->validated('name'),
        ]);

        return new SshKeyResource($sshKey);
    }

    public function destroy(SshKey $sshKey)
    {
        $sshKey->delete();

        return response()->noContent();
    }
}
