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
    public function __construct(
        private SecurityGroupService $securityGroupService
    ) {}
    
    public function index(Request $request)
    {
        $securityGroups = $this->securityGroupService->listSecurityGroups($request);

        return SecurityGroupResource::collection($securityGroups);
    }

    public function store(SecurityGroupRequest $request)
    {
        $securityGroup = $this->securityGroupService->create($request);
        
        return new SecurityGroupResource($securityGroup);
    }

    public function show(SecurityGroup $securityGroup)
    {
        $securityGroup = $this->securityGroupService->getSecurityGroup($securityGroup);

        return new SecurityGroupResource($securityGroup);
    }

    public function destroy(SecurityGroup $securityGroup)
    {
        $isAssociated = $this->securityGroupService->securityGroupAssociated($securityGroup);
        
        if ($isAssociated) {
            return response()->json([
                'message' => __('messages.security_groups.associated_servers_msg'),
            ], 422);
        }

        $this->securityGroupService->delete($securityGroup);

        return response()->noContent();
    }

}
