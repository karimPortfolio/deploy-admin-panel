<?php

namespace App\Enums;

enum StorageType: string
{
    case GP2 = 'gp2';
    case GP3 = 'gp3';

    case IO1 = 'io1';

    case STANDARD = 'standard';
 
    public function description(): string
    {
        return match($this) {
            self::GP2 => __('messages.rds_databases.storage_type.gp2'),
            self::GP3 => __('messages.rds_databases.storage_type.gp3'),
            self::IO1 => __('messages.rds_databases.storage_type.io1'),
            self::STANDARD => __('messages.rds_databases.storage_type.standard'),
        };
    }

    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'description' => $this->description(),
        ];
    }
}
