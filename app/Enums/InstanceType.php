<?php

namespace App\Enums;

enum InstanceType: string
{
    //free-tier eligible
    case T2Micro   = 't2.micro';
    case T3Micro   = 't3.micro';

    //general Purpose
    case T3Small   = 't3.small';
    case T3Medium  = 't3.medium';

    //compute Optimized
    case C5Large   = 'c5.large';
    case C5Xlarge  = 'c5.xlarge';

    //memory Optimized
    case R5Large   = 'r5.large';
    case R5Xlarge  = 'r5.xlarge';

    //GPU
    case G4dnXlarge = 'g4dn.xlarge';

    public function description(): string
    {
        return match($this) {
            self::T2Micro    => __('messages.servers.instance_type.description.T2Micro'),
            self::T3Micro    => __('messages.servers.instance_type.description.T3Micro'),
            self::T3Small    => __('messages.servers.instance_type.description.T3Small'),
            self::T3Medium   => __('messages.servers.instance_type.description.T3Medium'),
            self::C5Large    => __('messages.servers.instance_type.description.C5Large'),
            self::C5Xlarge   => __('messages.servers.instance_type.description.C5Xlarge'),
            self::R5Large    => __('messages.servers.instance_type.description.R5Large'),
            self::R5Xlarge   => __('messages.servers.instance_type.description.R5Xlarge'),
            self::G4dnXlarge => __('messages.servers.instance_type.description.G4dnXlarge'),
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
