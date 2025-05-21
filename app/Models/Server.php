<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    public function sshKey()
    {
        return $this->belongsTo(SshKey::class);
    }

    public function securityGroup()
    {
        return $this->belongsTo(SecurityGroup::class);
    }

}
