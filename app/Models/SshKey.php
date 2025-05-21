<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SshKey extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function servers()
    {
        return $this->hasMany(Server::class);
    }

}
