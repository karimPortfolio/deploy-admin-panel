<?php

namespace App\Console\Commands;

use App\Notifications\UnusedResourcesAlertNotification;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Notification;

class CheckUnusedResources extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-unused-resources';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command use to check unused resources and notify user about the unused resources.';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $users = $this->getAllUsers();

        foreach ($users as $user) {
            $this->info("Calculating the unused resources.");
            $data = $this->getData($user);

            if ($data['unused_security_groups_count'] > 0 || $data['unused_sshkeys_count'] > 0) {
                $this->info("Sending notification");
                $lang = $user->language ?? 'en';
                Notification::locale($lang)
                ->send($user, new UnusedResourcesAlertNotification($data));
            }
        }

        $this->info('Done');
    }

    private function getData(\App\Models\User $user): array
    {
        $unusedSecurityGroups = $this->getUnusedSecurityGroups($user);
        $unusedSshKeys = $this->getUnusedSshKeys($user);

        return [
            'unused_security_groups_count' => $unusedSecurityGroups->count(),
            'unused_sshkeys_count' => $unusedSshKeys->count(),
        ];
    }

    private function getUnusedSecurityGroups(\App\Models\User $user): Collection
    {
        return \App\Models\SecurityGroup::query()
            ->doesntHave('servers')
            ->where('created_by', $user->id)
            ->get();
    }

    private function getUnusedSshKeys(\App\Models\User $user): Collection
    {
        return \App\Models\SshKey::query()
            ->doesntHave('servers')
            ->where('created_by', $user->id)
            ->get();
    }

    private function getAllUsers(): Collection
    {
        return \App\Models\User::query()->get();
    }
}
