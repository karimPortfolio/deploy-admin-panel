<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\DBEngines;
use App\Enums\DBInstanceClass;
use App\Enums\DBStatus;
use App\Enums\ServerStatus;
use App\Enums\StorageType;
use App\Http\Controllers\Controller;
use App\Http\Requests\RdsDatabaseRequest;
use App\Http\Requests\RdsDatabaseServerAttachmentRequest;
use App\Http\Resources\RdsDatabaseResource;
use App\Jobs\CreateRdsDatabaseJob;
use App\Models\RdsDatabase;
use App\Models\Server;
use App\Services\RdsDatabaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;
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
                AllowedFilter::custom('created_at', new \App\Filters\DateFilter),
            ])
            ->allowedSorts(['id', 'allocated_storage', 'created_at'])
            ->with([
                'securityGroup:id,name,group_id,vpc_id',
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
            'snapshots' => function ($q) {
                $q->select('id', 'snapshot_identifier', 'rds_database_id', 'status', 'snapshot_type','created_at')
                ->orderBy('updated_at', 'desc')
                ->limit(3);
            }
        ]);

        return RdsDatabaseResource::make($rdsDatabase);
    }

    public function store(RdsDatabaseRequest $request)
    {
        $params = $this->getParamsFromRequest($request);

        $rdsDatabase = RdsDatabase::create([
            'db_instance_identifier' => $params['db_instance_identifier'],
            'db_instance_class' => $params['db_instance_class'],
            'engine' => $params['engine'],
            'db_name' => $params['db_name'],
            'master_username' => $params['master_username'],
            'master_password_encrypted' => Crypt::encryptString($params['master_user_password']),
            'storage_type' => $params['storage_type'],
            'backup_retention_period' => $params['backup_retention_period'],
            'publicly_accessible' => $params['publicly_accessible'],
            'storage_encrypted' => $params['storage_encrypted'],
            'multi_az' => $params['multi_az'],
            'allocated_storage' => $params['allocated_storage'],
            'status' => 'pending',
            'vpc_security_group' => $params['vpc_security_group'],
            'created_by' => $request->user()->id,
        ]);

        CreateRdsDatabaseJob::dispatch($rdsDatabase, $params);

        return new RdsDatabaseResource($rdsDatabase);
    }

    public function attachDatabaseToServer(RdsDatabaseServerAttachmentRequest $request)
    {
        $count = $this->getAttachedRdsDatabasesCountByServerId($request['server_id']);
        $isPrimary = $request->input('is_primary', false);
        $server = Server::find($request->input('server_id'));
        $database = RdsDatabase::find($request->input('rds_database_id'));

        if ($server->status !== ServerStatus::RUNNING) {
            return response()->json([
                'message' => __('messages.rds_databases.attach_server_not_running_msg'),
                'errors' => []
            ], 422);
        }

        if ($database->status !== DBStatus::STARTED) {
            return response()->json([
                'message' => __('messages.rds_databases.attach_database_not_started_msg'),
                'errors' => []
            ], 422);
        }

        return \DB::transaction(function () use ($request, $count, $isPrimary) {
            if ($count > 0 && $isPrimary === true) {
                \DB::table('rds_database_server')
                    ->where('server_id', $request->input('server_id'))
                    ->update(['is_primary' => false]);
            }

            if ($count === 0) {
                $isPrimary = true;
            }

            \DB::table('rds_database_server')->insert([
                'rds_database_id' => $request['rds_database_id'],
                'server_id' => $request['server_id'],
                'is_primary' => $isPrimary,
                'user_id' => $request->user()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->noContent();
        });
    }

    public function updatePrimaryDatabaseServerAttachment(string|int $id, RdsDatabaseServerAttachmentRequest $request)
    {
        \DB::table('rds_database_server')
        ->findOr($id, fn() => abort(404));

        return \DB::transaction(function () use ($request, $id) {
            \DB::table('rds_database_server')
                ->where('server_id', $request->input('server_id'))
                ->where('user_id', $request->user()->id)
                ->update(['is_primary' => false]);

            \DB::table('rds_database_server')
                ->where('id', $id)
                ->where('user_id', $request->user()->id)
                ->update(['is_primary' => $request->input('is_primary')]);

            return response()->noContent();
        });
    }

    public function detachDatabaseFromServer(string|int $id)
    {
        $attachment =  \DB::table('rds_database_server')
            ->findOr( $id, fn() => abort(404));

        if ($attachment->is_primary) {
            $this->changeTheDetachedServerPrimaryRandomly($attachment->server_id);
        }

        \DB::table('rds_database_server')
            ->where('id', $id)
            ->delete();
            
        return response()->noContent();
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

    private function rdsDatabaseAssociated(RdsDatabase $rdsDatabase)
    {
        $associatedServers = $rdsDatabase->servers;

        if ($associatedServers->count() > 0) {
            return true;
        }

        return false;
    }

    private function getParamsFromRequest(RdsDatabaseRequest $request): array
    {
        return [
            'db_instance_identifier' => $request->input('db_instance_identifier'),
            'db_instance_class' => DBInstanceClass::tryFrom($request->input('db_instance_class'))->value,
            'engine' => DBEngines::tryFrom($request->input('engine'))->value,
            'master_username' => $request->input('master_username'),
            'master_user_password' => $request->input('master_password'),
            'storage_type' => StorageType::tryFrom($request->input('storage_type'))->value,
            'db_name' => $request->input('db_name'),
            'backup_retention_period' => $request->input('backup_retention_period') ?? 7,
            'publicly_accessible' => $request->input('publicly_accessible') ?? false,
            'storage_encrypted' => $request->input('storage_encrypted') ?? false,
            'multi_az' => $request->input('multi_az') ?? false,
            'allocated_storage' => $request->input('allocated_storage'),
            'vpc_security_group' => $request->input('vpc_security_group'),
        ];
    }

    private function getAttachedRdsDatabasesCountByServerId(int $serverId): int
    {
        return \DB::table('rds_database_server')
            ->where('server_id', $serverId)
            ->count();
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
