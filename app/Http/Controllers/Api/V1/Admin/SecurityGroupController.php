<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\SecurityGroupResource;
use App\Models\SecurityGroup;
use App\Notifications\ResourceDeletedNotification;
use App\Services\SecurityGroupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

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

        $notifiable = \App\Models\User::find($securityGroup->created_by);
        $lang = $notifiable->language ?? 'en';
        
        Notification::locale($lang)
            ->send($notifiable, new ResourceDeletedNotification($securityGroup, 'security group', 'security-groups'));

        return response()->noContent();
    }
}
