<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RdsDatabase extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    protected $casts = [
        'storage_encrypted' => 'boolean',
        'publicly_accessible' => 'boolean',
        'multi_az' => 'boolean',
        'engine' => \App\Enums\DBEngines::class,
        'db_instance_class' => \App\Enums\DBInstanceClass::class,
        'storage_type' => \App\Enums\StorageType::class,
        'latest_restorable_time' => 'datetime',
        'instance_create_time' => 'datetime',
        'status' => \App\Enums\DBStatus::class,
    ];

    protected $hidden = [
        'master_password_encrypted',
    ];


    public function securityGroup()
    {
        return $this->belongsTo(SecurityGroup::class, 'vpc_security_group', 'group_id');
    }

    public function servers()
    {
        return $this->belongsToMany(Server::class, 'rds_database_server')
            ->withPivot('is_primary', 'user_id', 'id')
            ->withTimestamps();
    }

    public function snapshots()
    {
        return $this->hasMany(RdsDatabaseSnapshot::class, 'db_instance_identifier', 'db_instance_identifier');
    }
}
