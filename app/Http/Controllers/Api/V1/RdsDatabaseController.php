<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\RdsDatabaseRequest;
use App\Http\Requests\RdsDatabaseServerAttachmentRequest;
use App\Http\Resources\RdsDatabaseResource;
use App\Models\RdsDatabase;
use App\Services\RdsDatabaseService;
use Illuminate\Http\Request;


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

    public function store(RdsDatabaseRequest $request)
    {
        return new RdsDatabaseResource(
            $this->rdsDatabaseService->create($request)
        );
    }

    public function attachDatabaseToServer(RdsDatabaseServerAttachmentRequest $request)
    {
        $result = $this->rdsDatabaseService->attachDatabaseToServer($request);

        if (($result['success'] ?? false) === false) {
            return response()->json([
                'message' => $result['message'] ?? __('messages.unexpected_error'),
                'errors' => $result['errors'] ?? [],
            ], $result['status'] ?? 422);
        }

        return response()->noContent();
    }

    public function updatePrimaryDatabaseServerAttachment(string|int $id, RdsDatabaseServerAttachmentRequest $request)
    {
        $result = $this->rdsDatabaseService->updatePrimaryDatabaseServerAttachment($id, $request);

        if (($result['success'] ?? false) === false) {
            return response()->json([
                'message' => $result['message'] ?? __('messages.unexpected_error'),
                'errors' => $result['errors'] ?? [],
            ], $result['status'] ?? 422);
        }

        return response()->noContent();
    }

    public function detachDatabaseFromServer(string|int $id)
    {
        $this->rdsDatabaseService->detachDatabaseFromServer($id);
            
        return response()->noContent();
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
