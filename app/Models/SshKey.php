<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SshKey extends Model
{
    protected $guarded = [];

    public function servers()
    {
        return $this->hasMany(Server::class);
    }

}
