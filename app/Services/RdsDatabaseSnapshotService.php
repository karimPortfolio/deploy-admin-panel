<?php

namespace App\Services;

use App\Http\Requests\RdsDatabaseSnapshotRequest;
use App\Jobs\CreateRdsDatabaseSnapshotJob;
use App\Models\RdsDatabaseSnapshot;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class RdsDatabaseSnapshotService
{
    public function __construct(
        private \App\Services\AWS\RdsDatabaseSnapshotsService $awsRdsDatabaseSnapshotsService
    ){}

    public function listRdsDatabaseSnapshots(Request $request)
    {
        $search = $request->input('search');
        return QueryBuilder::for(RdsDatabaseSnapshot::class)
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
    }

    public function create(RdsDatabaseSnapshotRequest $request)
    {
        
        $dbInstanceIdentifier = $request->input('db_instance_identifier');
        $dbSnapshotIdentifier =  $dbInstanceIdentifier. '-snapshot-' . time();

        \App\Models\RdsDatabase::find($request->input('rds_database_id'))
        ->update([
            'status' => \App\Enums\DBStatus::BACKING_UP,
        ]);

        return CreateRdsDatabaseSnapshotJob::dispatch([
            'DBInstanceIdentifier' => $dbInstanceIdentifier,
            'DBSnapshotIdentifier' => $dbSnapshotIdentifier,
            'rds_database_id' => $request->input('rds_database_id'),
            'created_by' => $request->user()->id ?? null,
        ], $this->awsRdsDatabaseSnapshotsService);
    }

    public function getRdsDatabaseSnapshot(RdsDatabaseSnapshot $rdsDatabaseSnapshot)
    {
        return $rdsDatabaseSnapshot
                ->load('rdsDatabase:id,db_instance_identifier,db_name,engine');
    }

    public function delete(RdsDatabaseSnapshot $rdsDatabaseSnapshot)
    {
        $result = $this->awsRdsDatabaseSnapshotsService->deleteSnapshot($rdsDatabaseSnapshot->snapshot_identifier);

        if ($result['Status'] !== 'deleted') {
            throw new \Exception(__('messages.rds_databases.snapshots.delete_failed_msg'));
        }

        return $rdsDatabaseSnapshot->delete();
    }
}

