<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\VpcService;

class VpcController extends Controller
{
    public function __construct(
        private VpcService $vpcService
    ) {}

    public function __invoke()
    {
        return response()->json([
            'data' => $this->vpcService->listVpcs(),
        ]);
    }
}
