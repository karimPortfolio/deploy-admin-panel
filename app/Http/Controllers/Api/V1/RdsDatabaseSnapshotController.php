<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\DBSnapshotStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\RdsDatabaseSnapshotRequest;
use App\Http\Resources\RdsDatabaseSnapshotResource;
use App\Jobs\CreateRdsDatabaseSnapshotJob;
use App\Models\RdsDatabaseSnapshot;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class RdsDatabaseSnapshotController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $snapshots = QueryBuilder::for(RdsDatabaseSnapshot::class)
            ->allowedFilters([
                AllowedFilter::exact('rds_database_id'),
                AllowedFilter::exact('status'),
                AllowedFilter::custom('created_at', new \App\Filters\DateFilter()),
            ])
            ->allowedSorts(['id', 'created_at'])
            ->with([
                'rdsDatabase:id,db_instance_identifier',
            ])
            ->when($search, function ($query) use ($request, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('snapshot_identifier', 'like', "%{$search}%");
                });
            })
            ->orderBy('updated_at', 'desc')
            ->paginate($request->input('per_page', 15));
    
        
        return RdsDatabaseSnapshotResource::collection($snapshots);
    }

    public function show(RdsDatabaseSnapshot $rdsDatabaseSnapshot)
    {
        $rdsDatabaseSnapshot->load('rdsDatabase:id,db_instance_identifier,db_name,engine');

        return new RdsDatabaseSnapshotResource($rdsDatabaseSnapshot);
    }

    public function store(RdsDatabaseSnapshotRequest $request)
    {
        
        $dbInstanceIdentifier = $request->input('db_instance_identifier');
        $dbSnapshotIdentifier =  $dbInstanceIdentifier. '-snapshot-' . time();

        \App\Models\RdsDatabase::find($request->input('rds_database_id'))
        ->update([
            'status' => \App\Enums\DBStatus::BACKING_UP,
        ]);

        CreateRdsDatabaseSnapshotJob::dispatch([
            'DBInstanceIdentifier' => $dbInstanceIdentifier,
            'DBSnapshotIdentifier' => $dbSnapshotIdentifier,
            'rds_database_id' => $request->input('rds_database_id'),
            'created_by' => $request->user()->id ?? null,
        ]);

        return response()->json([
            'message' => __('messages.rds_databases.snapshots.create_initiated_msg'),
        ], 202);
    }

    public function destroy(RdsDatabaseSnapshot $rdsDatabaseSnapshot)
    {
        if (!in_array($rdsDatabaseSnapshot->status->value, [
            DBSnapshotStatus::STARTED->value,
            DBSnapshotStatus::FAILED->value,
        ])) {
            return response()->json([
                'message' => __('messages.rds_databases.snapshots.delete_not_allowed_msg'),
            ], 400);
        }

        $service = app(\App\Services\RdsDatabaseSnapshotsService::class);
        $result = $service->deleteSnapshot($rdsDatabaseSnapshot->snapshot_identifier);

        if ($result['Status'] !== 'deleted') {
            return response()->json([
                'message' => __('messages.rds_databases.snapshots.delete_failed_msg'),
            ], 500);
        }

        $rdsDatabaseSnapshot->delete();

        return response()->noContent();
    }
}
