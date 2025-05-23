<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\SecurityGroupRequest;
use App\Http\Resources\SecurityGroupResource;
use App\Models\SecurityGroup;
use App\Services\SecurityGroupService;
use Illuminate\Http\Request;

class SecurityGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $securityGroups = SecurityGroup::query()
            ->withCount('servers')
            ->orderBy('updated_at', 'desc')
            ->get();

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
        $securityGroup->load('servers');
        
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
