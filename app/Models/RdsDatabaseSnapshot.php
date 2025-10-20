<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RdsDatabaseSnapshot extends Model
{
    protected $guarded = [];

    protected $casts = [
        'snapshot_create_time' => 'datetime',
    ];

    protected $hidden = [
        'kms_key_id',
    ];

    public function rdsDatabase()
    {
        return $this->belongsTo(RdsDatabase::class, 'db_instance_identifier', 'db_instance_identifier');
    }
}
