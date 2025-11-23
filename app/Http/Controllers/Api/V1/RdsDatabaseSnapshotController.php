<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\DBSnapshotStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\RdsDatabaseSnapshotRequest;
use App\Http\Resources\RdsDatabaseSnapshotResource;
use App\Models\RdsDatabaseSnapshot;
use App\Services\RdsDatabaseSnapshotService;
use Illuminate\Http\Request;

class RdsDatabaseSnapshotController extends Controller
{
    public function __construct(
        private RdsDatabaseSnapshotService $rdsDatabaseSnapshotService
    ) {}
    public function index(Request $request)
    {
        $snapshots = $this->rdsDatabaseSnapshotService
                          ->listRdsDatabaseSnapshots($request);
        
        return RdsDatabaseSnapshotResource::collection($snapshots);
    }

    public function show(RdsDatabaseSnapshot $rdsDatabaseSnapshot)
    {
        $rdsDatabaseSnapshot = $this->rdsDatabaseSnapshotService
                                     ->getRdsDatabaseSnapshot($rdsDatabaseSnapshot);

        return new RdsDatabaseSnapshotResource($rdsDatabaseSnapshot);
    }

    public function store(RdsDatabaseSnapshotRequest $request)
    {
        $this->rdsDatabaseSnapshotService->create($request);

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

        $this->rdsDatabaseSnapshotService->delete($rdsDatabaseSnapshot);

        return response()->noContent();
    }
}
