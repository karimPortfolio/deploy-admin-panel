<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreatePermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create default permissions for the application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $permissions = $this->getPermissions();
        $this->info('Creating default permissions...');

        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission]);
        }

        $this->info('Default permissions have been created successfully.');
    }

    private function getPermissions(): array
    {
        return [
            '*',
            'servers.*',
            'servers.index',
            'servers.create',
            'servers.show',
            'servers.delete',
            'servers.start',
            'servers.stop',
            //security groups persmissions
            'security-groups.*',
            'security-groups.index',
            'security-groups.create',
            'security-groups.show',
            'security-groups.delete',
            //ssh keys permissions
            'ssh-keys.*',
            'ssh-keys.index',
            'ssh-keys.create',
            'ssh-keys.show',
            'ssh-keys.delete',
            //rds databases permissions
            'rds-databases.*',
            'rds-databases.index',
            'rds-databases.create',
            'rds-databases.show',
            'rds-databases.delete',
            'rds-databases.attach-to-server',
            'rds-databases.update-primary',
            'rds-databases.detach-from-server',
            'rds-databases.manage-backups',
            //rds database snapshots permissions
            'rds-database-snapshots.*',
            'rds-database-snapshots.index',
            'rds-database-snapshots.create',
            'rds-database-snapshots.show',
            'rds-database-snapshots.delete',
        ];
    }
}
