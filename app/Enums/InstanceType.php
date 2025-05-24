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
            self::T2Micro    => 'Free-tier eligible, general purpose',
            self::T3Micro    => 'General purpose, burstable performance',
            self::T3Small    => 'Small general purpose',
            self::T3Medium   => 'Medium general purpose',
            self::C5Large    => 'Compute-optimized, good for high-performance workloads',
            self::C5Xlarge   => 'Compute-optimized, extra capacity',
            self::R5Large    => 'Memory-optimized, good for data-intensive apps',
            self::R5Xlarge   => 'Memory-optimized, more RAM',
            self::G4dnXlarge => 'GPU instance, good for ML or graphics workloads',
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
