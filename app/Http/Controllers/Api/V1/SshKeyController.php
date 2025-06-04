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

    public function index()
    {
        $sshKeys = QueryBuilder::for(SshKey::class)
            ->allowedFilters(['name'])
            ->allowedSorts(['name', 'created_at'])
            ->withCount('servers')
            ->orderBy('updated_at', 'desc')
            ->get();

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
