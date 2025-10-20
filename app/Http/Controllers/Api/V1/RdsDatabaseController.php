<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\DBEngines;
use App\Enums\DBInstanceClass;
use App\Enums\DBStatus;
use App\Enums\StorageType;
use App\Http\Controllers\Controller;
use App\Http\Requests\RdsDatabaseRequest;
use App\Http\Resources\RdsDatabaseResource;
use App\Jobs\CreateRdsDatabaseJob;
use App\Models\RdsDatabase;
use App\Services\RdsDatabaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class RdsDatabaseController extends Controller
{
    public function index(Request $request)
    {
        $rds_databases = QueryBuilder::for(RdsDatabase::class)
            ->allowedFilters([
                AllowedFilter::exact('status'),
                AllowedFilter::exact('security_group_id', 'securityGroup.group_id'),
                AllowedFilter::exact('engine'),
                AllowedFilter::exact('db_instance_class'),
                AllowedFilter::exact('storage_type'),
                AllowedFilter::exact('status'),
                AllowedFilter::custom('created_at', new \App\Filters\DateFilter()),
            ])
            ->allowedSorts(['id', 'name', 'instance_id', 'public_ip_address', 'status', 'created_at'])
            ->with([
                'securityGroup:id,name,group_id',
            ])
            ->when($request->input('search'), function ($query) use ($request) {
                $query->where('db_name', 'like', '%' . $request->input('search') . '%')
                    ->orWhere('db_instance_identifier', 'like', '%' . $request->input('search') . '%')
                    ->orWhere('engine', 'like', '%' . $request->input('search') . '%');
            })
            ->orderBy('updated_at', 'desc')
            ->paginate($request->input('per_page', 15));


        return RdsDatabaseResource::collection($rds_databases);
    }

    public function show(RdsDatabase $rdsDatabase)
    {
        $rdsDatabase->load([
            'securityGroup:id,name,group_id',
            'server:id,instance_id,name,status,public_ip_address',
        ]);

        return response()->json($rdsDatabase);
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
        $engines = array_map(fn($engine) => $engine->toArray(), $engines);

        return response()->json($engines);
    }

    public function getDatabaseInstanceClasses()
    {
        $instanceClasses = DBInstanceClass::cases();
        $instanceClasses = array_map(fn($instanceClass) => $instanceClass->toArray(), $instanceClasses);

        return response()->json($instanceClasses);
    }

    public function getDatabaseStorageTypes()
    {
        $storageTypes = StorageType::cases();
        $storageTypes = array_map(fn($storageType) => $storageType->toArray(), $storageTypes);

        return response()->json($storageTypes);
    }

    public function getDatabaseStatuses()
    {
        $statuses = DBStatus::cases();
        $statuses = array_map(fn($status) => $status->toArray(), $statuses);
          
        return response()->json($statuses);
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
            'master_user_password' => $request->input('master_user_password'),
            'storage_type' => StorageType::tryFrom($request->input('storage_type'))->value,
            'db_name' => $request->input('db_name') ?? null,
            'backup_retention_period' => $request->input('backup_retention_period') ?? 7,
            'publicly_accessible' => $request->input('publicly_accessible') ?? false,
            'storage_encrypted' => $request->input('storage_encrypted') ?? false,
            'multi_az' => $request->input('multi_az') ?? false,
            'allocated_storage' => $request->input('allocated_storage'),
            'vpc_security_group' => $request->input('vpc_security_group'),
        ];
    }
}
