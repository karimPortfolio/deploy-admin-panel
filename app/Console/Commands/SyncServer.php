<?php

namespace App\Console\Commands;

use App\Enums\ServerStatus;
use App\Models\Server;
use App\Services\Ec2InstanceService;
use Illuminate\Console\Command;

class SyncServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-server';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check status of creating Servers and update them if ready';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $pendingServers = $this->getThePendingServers();
        $this->info('Found '.count($pendingServers).' pending servers to sync.');

        foreach ($pendingServers as $server) {
            try {
                
                if (!$server->instance_id) continue;

                $serverDetails = Ec2InstanceService::describeInstanceByInstanceId($server->instance_id);

                if (!empty($serverDetails['State'])) {
                    $server->update([
                        'status' => ServerStatus::tryFrom($serverDetails['State']['Name'])->value,
                        'public_ip_address' => $serverDetails['PublicIpAddress'],
                    ]);
                }

            } catch (\Exception $e) {
                \Log::error('Error syncing Server ID '.$server->id.': '.$e->getMessage());
            }
        }

        $this->info('Server sync completed.');
    }

    private function getThePendingServers()
    {
        return Server::where('status', ServerStatus::PENDING)
        ->get();
    }

}
