<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\SecurityGroupRequest;
use App\Http\Resources\SecurityGroupResource;
use App\Models\SecurityGroup;
use App\Services\SecurityGroupService;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class SecurityGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $securityGroups = QueryBuilder::for(SecurityGroup::class)
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
            ->paginate(function ($total) use ($request) {
                $perPage = $request->integer('per_page', -1);

                if ($perPage === 0) {
                    $perPage = $total;
                } elseif ($perPage === -1) {
                    $perPage = 10;
                }

                return $perPage;
            });

        return SecurityGroupResource::collection($securityGroups);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(SecurityGroupRequest $request)
    {
        $name = $request->validated('name');
        $description = $request->validated('description');

        return \DB::transaction(function () use ($name, $description) {
            $securityGroup = SecurityGroupService::createSecurityGroup($name, $description);

            $securityGroup = SecurityGroup::create([
                'group_id' => $securityGroup['GroupId'],
                'name' => $name,
                'description' => $description,
                'vpc_id' => $securityGroup['vpc_id'],
            ]);

            return new SecurityGroupResource($securityGroup);
        });

    }

    /**
     * Display the specified resource.
     */
    public function show(SecurityGroup $securityGroup)
    {
        $securityGroup->load([
            'servers:id,name,instance_id,status,security_group_id',
            'createdBy:id,name',
        ]);

        return new SecurityGroupResource($securityGroup);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SecurityGroup $securityGroup)
    {
        if ($this->securityGroupAssociated($securityGroup)) {
            return response()->json([
                'message' => 'Security group is associated with servers and cannot be deleted.',
            ], 422);
        }

        SecurityGroupService::deleteSecurityGroup($securityGroup->group_id);
        $securityGroup->delete();

        return response()->noContent();
    }

    private function securityGroupAssociated(SecurityGroup $securityGroup)
    {
        $associatedServers = $securityGroup->servers;

        if ($associatedServers->count() > 0) {
            return true;
        }

        return false;
    }
}
