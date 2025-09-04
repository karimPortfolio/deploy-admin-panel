<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\SecurityGroupResource;
use App\Models\SecurityGroup;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class SecurityGroupController extends Controller
{
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

    public function show(SecurityGroup $securityGroup)
    {
        $securityGroup->load([
            'servers:id,name,instance_id,status,security_group_id',
            'createdBy:id,name',
        ]);

        return new SecurityGroupResource($securityGroup);
    }

    public function destroy(SecurityGroup $securityGroup)
    {
        $securityGroup->delete();

        return response()->noContent();
    }
}
