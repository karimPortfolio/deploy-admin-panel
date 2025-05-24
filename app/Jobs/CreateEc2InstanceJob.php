<?php

namespace App\Jobs;

use App\Models\Server;
use App\Services\Ec2InstanceService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateEc2InstanceJob implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected Server $server, protected array $params)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        \Log::info('EC2 Job started with params');

        try {
            $newEc2Instance = Ec2InstanceService::createInstance($this->params);
            $ec2Instance = Ec2InstanceService::describeInstanceByInstanceId([$newEc2Instance['InstanceId']]);

            $this->server->update([
                'instance_id' => $newEc2Instance['InstanceId'],
                'image_id' => $newEc2Instance['ImageId'],
                'status' => $ec2Instance['State']['Name'],//this one waits until the instance is running
                'private_ip_address' => $newEc2Instance['PrivateIpAddress'],
                'public_ip_address' => $ec2Instance['PublicIpAddress'],//this one waites until the ip public address is assigned
                'vpc_id' => $newEc2Instance['VpcId'],
                'subnet_id' => $newEc2Instance['SubnetId'],
            ]);
        }
        catch (\Exception $e) {
            $this->server->update(['status' => 'failed']);
            \Log::error('EC2 creation failed', ['error' => $e->getMessage()]);
        }
    }
}
