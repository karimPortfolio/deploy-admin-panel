<?php

namespace App\Models;

use App\Enums\InstanceType;
use App\Enums\OsFamily;
use App\Enums\ServerStatus;
use App\Scopes\AuthenticatedUserScope;
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

    protected static function booted()
    {
        if (!app()->runningInConsole()) {
            static::addGlobalScope(new AuthenticatedUserScope);
        }
    }

    public function sshKey()
    {
        return $this->belongsTo(SshKey::class);
    }

    public function securityGroup()
    {
        return $this->belongsTo(SecurityGroup::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function rdsDatabases()
    {
        return $this->belongsToMany(RdsDatabase::class, 'rds_database_server')
            ->withPivot('is_primary', 'user_id')
            ->withTimestamps();
    }

    public function keyPair()
    {
        return $this->belongsTo(ServerKey::class, 'key_id');
    }

}
