<?php

namespace App\Enums;

enum DBStatus: string
{
    case STARTED = 'started';
    case STOPPED = 'stopped';
    case BACKING_UP = 'backing_up';
    case PENDING = 'pending';
    case FAILED = 'failed';


    public function label(): string
    {
        return match($this) {
            self::STARTED   => __('messages.rds_databases.status.started'),
            self::STOPPED   => __('messages.rds_databases.status.stopped'),
            self::BACKING_UP => __('messages.rds_databases.status.backing_up'),
            self::PENDING   => __('messages.servers.status.pending'),
            self::FAILED   => __('messages.rds_databases.status.failed'),
        };
    }

    public function color(): string
    {
        return match($this) {
            self::STARTED   => 'positive',
            self::STOPPED   => 'negative',
            self::BACKING_UP   => 'blue-grey',
            self::PENDING   => 'primary',
            self::FAILED => 'warning',
        };
    }

    public function hexColor(): string
    {
        return match($this) {
            self::STARTED   => '#21BA45',
            self::STOPPED   => '#C10015',
            self::FAILED => '#F2C037',
            self::PENDING   => '#1976d2',
            self::BACKING_UP   => '#607d8b',
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
