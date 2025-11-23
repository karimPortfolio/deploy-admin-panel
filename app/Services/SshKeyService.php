<?php

namespace App\Services;

use App\Http\Requests\SshKeyRequest;
use App\Models\SshKey;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class SshKeyService
{
    public function __construct(
        private \App\Services\AWS\SshKeyService $awsSshKeyService
    ){}

    public function listSshKeys(Request $request): LengthAwarePaginator
    {
        return QueryBuilder::for(SshKey::class)
            ->allowedFilters([
                'name',
                'created_at',
            ])
            ->allowedSorts(['name', 'created_at', 'id'])
            ->withCount('servers')
            ->with([
                'createdBy:id,name',
            ])
            ->when($request->input('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('public_key', 'like', "%{$search}%");
            })
            ->orderBy('updated_at', 'desc')
            ->paginate(function ($total) use ($request) {
                $perPage = $request->integer('per_page', -1);

                if ($perPage === 0) {
                    $perPage = $total;
                } elseif ($perPage === -1) {
                    $perPage = 10;
                }

                return $perPage;
            });
    }

    public function create(SshKeyRequest $request)
    {
        return \DB::transaction(function () use ($request) {
            // create the ssh  public and private keys
            $keys = $this->awsSshKeyService->createSshKey($request->validated('name'));

            // store the ssh key
            $newSshKey = SshKey::create([
                'name' => $request->validated('name'),
                'public_key' => $keys['public_key'],
                'private_key' => $keys['private_key'],
                'created_by' => auth()->id(),
            ]);

            return $newSshKey;
        });
    }

    public function getSshKey(SshKey $sshKey): SshKey
    {
        return $sshKey->load(['servers:id,instance_id,status,ssh_key_id', 'createdBy:id,name']);
    }

    public function update(SshKeyRequest $request, SshKey $sshKey): SshKey
    {
        $sshKey->update([
            'name' => $request->validated('name'),
        ]);

        return $sshKey;
    }

    public function delete(SshKey $sshKey)
    {
        return $sshKey->delete();
    }

    public function sshKeyAssociated(SshKey $sshKey)
    {
        $associatedServers = $sshKey->servers;

        if ($associatedServers->count() > 0) {
            return true;
        }

        return false;
    }
}

