<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Enums\DBEngines;
use App\Enums\DBInstanceClass;
use App\Enums\DBStatus;
use App\Enums\ServerStatus;
use App\Enums\StorageType;
use App\Http\Controllers\Controller;
use App\Http\Resources\RdsDatabaseResource;
use App\Models\RdsDatabase;
use App\Notifications\ResourceDeletedNotification;
use App\Services\RdsDatabaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class RdsDatabaseController extends Controller
{
    public function index(Request $request)
    {
        $rds_databases = QueryBuilder::for(RdsDatabase::class)
            ->allowedFilters([
                AllowedFilter::exact('status'),
                AllowedFilter::exact('vpc_security_group', 'securityGroup.group_id'),
                AllowedFilter::exact('engine'),
                AllowedFilter::exact('db_instance_class'),
                AllowedFilter::exact('storage_type'),
                AllowedFilter::exact('status'),
                AllowedFilter::exact('created_by'),
                AllowedFilter::custom('created_at', new \App\Filters\DateFilter),
            ])
            ->allowedSorts(['id', 'allocated_storage', 'created_at'])
            ->with([
                'securityGroup:id,name,group_id,vpc_id',
                'createdBy:id,name',
            ])
            ->when($request->input('search'), function ($query) use ($request) {
                $query->where('db_name', 'like', '%'.$request->input('search').'%')
                    ->orWhere('db_instance_identifier', 'like', '%'.$request->input('search').'%')
                    ->orWhere('engine', 'like', '%'.$request->input('search').'%');
            })
            ->orderBy('updated_at', 'desc')
            ->paginate($request->input('per_page', 15));

        return RdsDatabaseResource::collection($rds_databases);
    }

    public function show(RdsDatabase $rdsDatabase)
    {
        $rdsDatabase->load([
            'securityGroup:id,name,group_id',
            'servers:id,instance_id,name,status,public_ip_address',
            'createdBy:id,name',
        ]);

        return RdsDatabaseResource::make($rdsDatabase);
    }


    public function destroy(RdsDatabase $rdsDatabase)
    {
        if ($this->rdsDatabaseAssociated($rdsDatabase)) {
            return response()->json([
                'message' => __('messages.rds_databases.associated_servers_msg'),
            ], 422);
        }

        $result = RdsDatabaseService::deleteRdsDatabaseByInstanceId($rdsDatabase->db_instance_identifier);
        if ($result->getPath('DBInstance.DBInstanceStatus') !== 'deleting') {
            return response()->json([
                'message' => __('messages.rds_databases.delete_failed_msg'),
            ], 500);
        }

        $rdsDatabase->delete();

        $notifiable = \App\Models\User::find($rdsDatabase->created_by);
        Notification::send(
            $notifiable, 
            new ResourceDeletedNotification(
                $rdsDatabase, 
                __('messages.rds_databases.title')
                , 'rds-databases')
        );

        return response()->noContent();
    }

    public function getDatabaseEngines()
    {
        $engines = DBEngines::cases();
        $engines = array_map(fn ($engine) => $engine->toArray(), $engines);

        return response()->json([
            'data' => $engines,
        ]);
    }

    public function getDatabaseInstanceClasses()
    {
        $instanceClasses = DBInstanceClass::cases();
        $instanceClasses = array_map(fn ($instanceClass) => $instanceClass->toArray(), $instanceClasses);

        return response()->json([
            'data' => $instanceClasses,
        ]);
    }

    public function getDatabaseStorageTypes()
    {
        $storageTypes = StorageType::cases();
        $storageTypes = array_map(fn ($storageType) => $storageType->toArray(), $storageTypes);

        return response()->json([
            'data' => $storageTypes,
        ]);
    }

    public function getDatabaseStatuses()
    {
        $statuses = DBStatus::cases();
        $statuses = array_map(fn ($status) => $status->toArray(), $statuses);

        return response()->json([
            'data' => $statuses,
        ]);
    }

    public function getServers()
    {
        $servers = \App\Models\Server::query()
            ->select('id', 'instance_id', 'name')
            ->get();

        return response()->json([
            'data' => $servers,
        ]);
    }

    public function getUsers()
    {
        $users = \App\Models\User::query()
            ->select('id', 'name')
            ->get();

        return response()->json([
            'data' => $users,
        ]);
    }

    private function rdsDatabaseAssociated(RdsDatabase $rdsDatabase)
    {
        $associatedServers = $rdsDatabase->servers;

        if ($associatedServers->count() > 0) {
            return true;
        }

        return false;
    }

    private function changeTheDetachedServerPrimaryRandomly(int $serverId)
    {
        $attachment = \DB::table('rds_database_server')
            ->where('server_id', $serverId)
            ->where('is_primary', false)
            ->inRandomOrder()
            ->first();

        if ($attachment) {
            \DB::table('rds_database_server')
                ->where('id', $attachment->id)
                ->update(['is_primary' => true]);
        }
    }
}
