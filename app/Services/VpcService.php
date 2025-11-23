<?php

namespace App\Services;

class VpcService
{

    public function __construct(
        private \App\Services\AWS\VpcService $awsVpcService
    ){}

    public function listVpcs()
    {
        $vpcs = $this->awsVpcService->listVpcs();

        $vpcs = array_map(function ($vpc) {
            return [
                'vpc_id' => $vpc['VpcId'],
                'state' => $vpc['State'],
                'cidr_block' => $vpc['CidrBlock'],
            ];
        }, $vpcs);

        return $vpcs;
    }
}

