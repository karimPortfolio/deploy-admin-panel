<?php

namespace App\Jobs;

use App\Models\Server;
use App\Models\ServerKey;
use App\Services\AWS\Ec2InstanceService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Crypt;

class CreateEc2InstanceJob implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected Server $server, 
        protected array $params,
    ){}

    /**
     * Execute the job.
     */
    public function handle(Ec2InstanceService $awsEc2InstanceService): void
    {
        try {
            $keyPair = $awsEc2InstanceService->createKeyPair($this->params['name']);
            $this->params['key_name'] = $keyPair['KeyName'];
            $newEc2Instance = $awsEc2InstanceService->createInstance($this->params);

            $newKeyPair = ServerKey::create([
                'key_name' => $keyPair['KeyName'],
                'key_content' => Crypt::encryptString($keyPair['KeyMaterial']),
                'created_by' => $this->params['created_by'] 
            ]);

            $this->server->update([
                'instance_id' => $newEc2Instance['InstanceId'],
                'image_id' => $newEc2Instance['ImageId'],
                'private_ip_address' => $newEc2Instance['PrivateIpAddress'],
                'vpc_id' => $newEc2Instance['VpcId'],
                'subnet_id' => $newEc2Instance['SubnetId'],
                'key_id' => $newKeyPair->id
            ]);
        }
        catch (\Exception $e) {
            $this->server->forceDelete();
            \Log::error('EC2 creation failed', ['error' => $e->getMessage()]);
        }
    }
}
