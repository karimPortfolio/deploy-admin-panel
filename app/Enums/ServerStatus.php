<?php

namespace App\Enums;

enum ServerStatus: string
{
    case RUNNING = 'running';
    case STOPPED = 'stopped';
    case TERMINATED = 'terminated';
    case PENDING = 'pending';

    public function label(): string
    {
        return match($this) {
            self::RUNNING   => 'Running',
            self::STOPPED   => 'Stopped',
            self::TERMINATED => 'Terminated',
            self::PENDING   => 'Pending',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::RUNNING   => 'success',
            self::STOPPED   => 'warning',
            self::TERMINATED => 'danger',
            self::PENDING   => 'info',
        };
    }

    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'label' => $this->label(),
            'icon' => $this->color(),
        ];
    }
    
}
