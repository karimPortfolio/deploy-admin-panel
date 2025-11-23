<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Enums\DBEngines;
use App\Enums\DBInstanceClass;
use App\Enums\DBStatus;
use App\Enums\StorageType;
use App\Http\Controllers\Controller;
use App\Http\Resources\RdsDatabaseResource;
use App\Models\RdsDatabase;
use App\Notifications\ResourceDeletedNotification;
use App\Services\AWS\RdsDatabaseService as AwsRdsDatabaseService;
use App\Services\RdsDatabaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class RdsDatabaseController extends Controller
{
    public function __construct(
        private RdsDatabaseService $rdsDatabaseService
    ){}


    public function index(Request $request)
    {
        return RdsDatabaseResource::collection(
            $this->rdsDatabaseService->listRdsDatabases($request)
        );
    }

    public function show(RdsDatabase $rdsDatabase)
    {
        return RdsDatabaseResource::make(
            $this->rdsDatabaseService->getRdsDatabase($rdsDatabase)
        );
    }

     public function destroy(RdsDatabase $rdsDatabase)
    {
        $result = $this->rdsDatabaseService->destroy($rdsDatabase);

        if (($result['success'] ?? false) === false) {
            return response()->json([
                'message' => $result['message'] ?? __('messages.unexpected_error'),
                'errors' => $result['errors'] ?? [],
            ], $result['status'] ?? 422);
        }

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
        return response()->json([
            'data' => $this->rdsDatabaseService->databaseEngines(),
        ]);
    }

    public function getDatabaseInstanceClasses()
    {
        return response()->json([
            'data' => $this->rdsDatabaseService->databaseInstanceClasses(),
        ]);
    }

    public function getDatabaseStorageTypes()
    {
        return response()->json([
            'data' => $this->rdsDatabaseService->databaseStorageTypes(),
        ]);
    }

    public function getDatabaseStatuses()
    {
        return response()->json([
            'data' => $this->rdsDatabaseService->databaseStatuses(),
        ]);
    }

    public function getServers()
    {
        return response()->json([
            'data' => $this->rdsDatabaseService->serversList(),
        ]);
    }
}
