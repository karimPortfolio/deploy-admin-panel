<?php

namespace App\Services;

use App\Http\Requests\SecurityGroupRequest;
use App\Models\SecurityGroup;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class SecurityGroupService
{
    public function __construct(
        private \App\Services\AWS\SecurityGroupService $awsSecurityGroupService
    ){}

    public function listSecurityGroups(Request $request): LengthAwarePaginator
    {
        return QueryBuilder::for(SecurityGroup::class)
            ->allowedFilters([
                AllowedFilter::exact('vpc_id'),
                AllowedFilter::custom('created_at', new \App\Filters\DateFilter()),
            ])
            ->allowedSorts(['id', 'name', 'description', 'vpc_id', 'group_id', 'created_at', 'updated_at'])
            ->withCount('servers')
            ->with([
                'createdBy:id,name',
            ])
            ->when($request->input('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('group_id', 'like', "%{$search}%");
                });
            })
            ->orderBy('updated_at', 'desc')
            ->paginate($request->input('per_page', 15));
    }

    public function create(SecurityGroupRequest $request)
    {
        $name = $request->validated('name');
        $description = $request->validated('description');

        return \DB::transaction(function () use ($name, $description) {
            $securityGroup = $this->awsSecurityGroupService->createSecurityGroup($name, $description);

            $securityGroup = SecurityGroup::create([
                'group_id' => $securityGroup['GroupId'],
                'name' => $name,
                'description' => $description,
                'vpc_id' => $securityGroup['vpc_id'],
                'created_by' => auth()->id()
            ]);

            return $securityGroup;
        });
    }

    public function getSecurityGroup(SecurityGroup $securityGroup): SecurityGroup
    {
        $securityGroup->load([
            'servers:id,name,instance_id,status,security_group_id',
            'createdBy:id,name',
        ]);

        return $securityGroup;
    }

    public function delete(SecurityGroup $securityGroup)
    {
        $this->awsSecurityGroupService->deleteSecurityGroup($securityGroup->group_id);
        return $securityGroup->delete();
    }

    public function securityGroupAssociated(SecurityGroup $securityGroup)
    {
        $associatedServers = $securityGroup->servers;

        if ($associatedServers->count() > 0) {
            return true;
        }

        return false;
    }
}

