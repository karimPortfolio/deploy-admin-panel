<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\SshKeyRequest;
use App\Http\Resources\SshKeyResource;
use App\Models\SshKey;
use App\Services\SshKeyService;
use Illuminate\Http\Request;

class SshKeyController extends Controller
{

     public function __construct(
        private SshKeyService $sshKeyService
     ) {}

    public function index(Request $request)
    {
        $sshKeys = $this->sshKeyService->listSshKeys($request);

        return SshKeyResource::collection($sshKeys);
    }

    public function store(SshKeyRequest $request)
    {
        $newSshKey = $this->sshKeyService->create($request);
        return new SshKeyResource($newSshKey);
    }

    public function show(SshKey $sshKey)
    {
        $newSshKey = $this->sshKeyService->getSshKey($sshKey);

        return new SshKeyResource($sshKey);
    }

    public function update(SshKeyRequest $request, SshKey $sshKey)
    {
        $sshKey = $this->sshKeyService->update($request, $sshKey);

        return new SshKeyResource($sshKey);
    }

    public function destroy(SshKey $sshKey)
    {
        $isAssociated = $this->sshKeyService->sshKeyAssociated($sshKey);
        if ($isAssociated) {
            return response()->json([
                'message' => __('messages.ssh_keys.associated_servers_msg'),
            ], 422);
        }

        $this->sshKeyService->delete($sshKey);
        
        return response()->noContent();
    }
}
