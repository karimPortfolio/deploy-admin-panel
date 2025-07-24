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
            self::RUNNING   => 'positive',
            self::STOPPED   => 'negative',
            self::TERMINATED => 'warning',
            self::PENDING   => 'primary',
        };
    }

    public function hexColor(): string
    {
        return match($this) {
            self::RUNNING   => '#21BA45',
            self::STOPPED   => '#C10015',
            self::TERMINATED => '#F2C037',
            self::PENDING   => '#1976d2',
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
