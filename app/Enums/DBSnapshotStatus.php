<?php

namespace App\Enums;

enum DBSnapshotStatus: string
{
    case STARTED = 'started';
    case CREATING = 'creating';
    case COPYING = 'copying';
    case PENDING = 'pending';
    case DELETING = 'deleting';
    case FAILED = 'failed';


    public function label(): string
    {
        return match($this) {
            self::STARTED   => __('messages.rds_databases.snapshots.status.started'),
            self::CREATING  => __('messages.rds_databases.snapshots.status.creating'),
            self::COPYING   => __('messages.rds_databases.snapshots.status.copying'),
            self::PENDING   => __('messages.rds_databases.snapshots.status.pending'),
            self::DELETING  => __('messages.rds_databases.snapshots.status.deleting'),
            self::FAILED    => __('messages.rds_databases.snapshots.status.failed'),
        };
    }

    public function color(): string
    {
        return match($this) {
            self::STARTED   => 'positive',
            self::CREATING  => 'secondary',
            self::COPYING   => 'dark',
            self::PENDING   => 'primary',
            self::DELETING  => 'warning',
            self::FAILED    => 'negative',
        };
    }

    public function hexColor(): string
    {
        return match($this) {
            self::STARTED   => '#21BA45',
            self::COPYING   => '#2185D0',
            self::PENDING   => '#1976d2',
            self::DELETING  => '#F2C037',
            self::FAILED    => '#C10015',
        };
    }

    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'label' => $this->label(),
            'color' => $this->color(),
        ];
    }
    
}
