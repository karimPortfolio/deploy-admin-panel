<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\SshKeyResource;
use App\Models\SshKey;
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

        return SshKeyResource::collection($sshKeys);
    }

    public function show(SshKey $sshKey)
    {
        $sshKey->load(['servers:id,instance_id,status,ssh_key_id', 'createdBy:id,name']);

        return new SshKeyResource($sshKey);
    }

    public function destroy(SshKey $sshKey)
    {
        $sshKey->delete();

        return response()->noContent();
    }
}
