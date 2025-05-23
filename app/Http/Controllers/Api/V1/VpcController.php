<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\VpcResource;
use App\Services\VpcService;
use Illuminate\Http\Request;

class VpcController extends Controller
{
    

    public function __invoke()
    {
        $vpcs = VpcService::listVpcs();

        $vpcs = array_map(function ($vpc) {
            return [
                'vpc_id' => $vpc['VpcId'],
                'state' => $vpc['State'],
                'cidr_block' => $vpc['CidrBlock'],
            ];
        }, $vpcs);

        return response()->json([
            'data' => $vpcs,
        ]);
    }
}
