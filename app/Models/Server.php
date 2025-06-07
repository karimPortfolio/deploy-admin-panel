<?php

namespace App\Models;

use App\Enums\InstanceType;
use App\Enums\OsFamily;
use App\Enums\ServerStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    protected $casts = [
        'os_family' => OsFamily::class,
        'instance_type' => InstanceType::class,
        'status' => ServerStatus::class,
    ];

    public function sshKey()
    {
        return $this->belongsTo(SshKey::class);
    }

    public function securityGroup()
    {
        return $this->belongsTo(SecurityGroup::class);
    }

}
