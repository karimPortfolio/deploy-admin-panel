<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RdsDatabaseSnapshot extends Model
{

    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'snapshot_create_time' => 'datetime',
        'status' => \App\Enums\DBSnapshotStatus::class,
    ];

    protected $hidden = [
        'kms_key_id',
    ];

    public function rdsDatabase()
    {
        return $this->belongsTo(RdsDatabase::class, 'rds_database_id', 'id');
    }
}
