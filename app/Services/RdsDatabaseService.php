<?php

namespace App\Services;

use App\Enums\DBEngines;
use App\Enums\DBInstanceClass;
use App\Enums\DBStatus;
use App\Enums\ServerStatus;
use App\Enums\StorageType;
use App\Http\Requests\RdsDatabaseRequest;
use App\Http\Requests\RdsDatabaseServerAttachmentRequest;
use App\Jobs\CreateRdsDatabaseJob;
use App\Models\RdsDatabase;
use App\Models\Server;
use App\Services\AWS\RdsDatabaseService as AwsRdsDatabaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class RdsDatabaseService
{
    public function __construct(
        private readonly AwsRdsDatabaseService $awsRdsDatabaseService
    ) {}


    public function listRdsDatabases(Request $request): mixed
    {
        return QueryBuilder::for(RdsDatabase::class)
            ->allowedFilters(
                $this->getFiltersArray()
            )
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
    }

    public function create(RdsDatabaseRequest $request): RdsDatabase
    {
        $params = $this->getParamsFromRequest($request);

        return \DB::transaction(function () use ($params, $request) {
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

            return $rdsDatabase;
        });
    }

    public function getRdsDatabase(RdsDatabase $rdsDatabase): RdsDatabase
    {
        return $rdsDatabase->load([
            'securityGroup:id,name,group_id',
            'servers:id,instance_id,name,status,public_ip_address',
            'snapshots' => function ($q) {
                $q->select('id', 'snapshot_identifier', 'rds_database_id', 'status', 'snapshot_type','created_at')
                ->orderBy('updated_at', 'desc')
                ->limit(3);
            }
        ]);
    }

    public function attachDatabaseToServer(RdsDatabaseServerAttachmentRequest $request): array
    {
        $count = $this->getAttachedRdsDatabasesCountByServerId($request->input('server_id'));
        $isPrimary = $request->boolean('is_primary');
        $server = Server::find($request->input('server_id'));
        $database = RdsDatabase::find($request->input('rds_database_id'));

        if (!$server) {
            return [
                'success' => false,
                'status' => 404,
                'message' => __('messages.servers.not_found'),
            ];
        }

        if ($server->status !== ServerStatus::RUNNING) {
            return [
                'success' => false,
                'status' => 422,
                'message' => __('messages.rds_databases.attach_server_not_running_msg'),
            ];
        }

        if (!$database) {
            return [
                'success' => false,
                'status' => 404,
                'message' => __('messages.rds_databases.not_found'),
            ];
        }

        if ($database->status !== DBStatus::STARTED) {
            return [
                'success' => false,
                'status' => 422,
                'message' => __('messages.rds_databases.attach_database_not_started_msg'),
            ];
        }

        return DB::transaction(function () use ($request, $count, $isPrimary) {
            if ($count > 0 && $isPrimary === true) {
                DB::table('rds_database_server')
                    ->where('server_id', $request->input('server_id'))
                    ->update(['is_primary' => false]);
            }

            if ($count === 0) {
                $isPrimary = true;
            }

            DB::table('rds_database_server')->insert([
                'rds_database_id' => $request->input('rds_database_id'),
                'server_id' => $request->input('server_id'),
                'is_primary' => $isPrimary,
                'user_id' => $request->user()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return ['success' => true];
        });
    }

    public function updatePrimaryDatabaseServerAttachment(int|string $attachmentId, RdsDatabaseServerAttachmentRequest $request): array
    {
        $attachment = DB::table('rds_database_server')
            ->where('id', $attachmentId)
            ->first();

        if (!$attachment) {
            return [
                'success' => false,
                'status' => 404,
                'message' => __('messages.rds_databases.attachment_not_found'),
            ];
        }

        DB::transaction(function () use ($request, $attachmentId) {
            DB::table('rds_database_server')
                ->where('server_id', $request->input('server_id'))
                ->where('user_id', $request->user()->id)
                ->update(['is_primary' => false]);

            DB::table('rds_database_server')
                ->where('id', $attachmentId)
                ->where('user_id', $request->user()->id)
                ->update(['is_primary' => $request->boolean('is_primary')]);
        });

        return ['success' => true];
    }

    public function destroy(RdsDatabase $rdsDatabase): array
    {
        if ($this->rdsDatabaseAssociated($rdsDatabase)) {
            return [
                'success' => false,
                'status' => 422,
                'message' => __('messages.rds_databases.associated_servers_msg'),
            ];
        }

        $result = $this->awsRdsDatabaseService->deleteRdsDatabaseByInstanceId($rdsDatabase->db_instance_identifier);

        if ($result->getPath('DBInstance.DBInstanceStatus') !== 'deleting') {
            return [
                'success' => false,
                'status' => 500,
                'message' => __('messages.rds_databases.delete_failed_msg'),
            ];
        }

        $rdsDatabase->delete();

        return ['success' => true];
    }

    public function detachDatabaseFromServer(string|int $id)
    {
        $attachment =  \DB::table('rds_database_server')
            ->findOr( $id, fn() => abort(404));

        if ($attachment->is_primary) {
            $this->changeTheDetachedServerPrimaryRandomly($attachment->server_id);
        }

        return \DB::table('rds_database_server')
            ->where('id', $id)
            ->delete();
    }

    public function databaseEngines(): array
    {
        return array_map(static fn (DBEngines $engine) => $engine->toArray(), DBEngines::cases());
    }

    public function databaseInstanceClasses(): array
    {
        return array_map(static fn (DBInstanceClass $instanceClass) => $instanceClass->toArray(), DBInstanceClass::cases());
    }

    public function databaseStorageTypes(): array
    {
        return array_map(static fn (StorageType $storageType) => $storageType->toArray(), StorageType::cases());
    }

    public function databaseStatuses(): array
    {
        return array_map(static fn (DBStatus $status) => $status->toArray(), DBStatus::cases());
    }

    public function serversList(): array
    {
        return Server::query()
            ->select('id', 'instance_id', 'name')
            ->get()
            ->toArray();
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

    private function rdsDatabaseAssociated(RdsDatabase $rdsDatabase): bool
    {
        return $rdsDatabase->servers()->exists();
    }

     private function getFiltersArray(): array
    {
        $filtersArray = [
                AllowedFilter::exact('status'),
                AllowedFilter::exact('vpc_security_group', 'securityGroup.group_id'),
                AllowedFilter::exact('engine'),
                AllowedFilter::exact('db_instance_class'),
                AllowedFilter::exact('storage_type'),
                AllowedFilter::exact('status'),
                AllowedFilter::custom('created_at', new \App\Filters\DateFilter),
        ];

        if (auth()->user()->isAdmin())
        {
            array_push($filtersArray, AllowedFilter::exact('created_by'));
        }

        return $filtersArray;
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
        return DB::table('rds_database_server')
            ->where('server_id', $serverId)
            ->count();
    }
}