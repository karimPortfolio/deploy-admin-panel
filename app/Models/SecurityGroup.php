<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecurityGroup extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    public function servers()
    {
        return $this->hasMany(Server::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
