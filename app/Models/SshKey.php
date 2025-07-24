<?php

namespace App\Models;

use App\Scopes\AuthenticatedUserScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SshKey extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted()
    {
        static::addGlobalScope(new AuthenticatedUserScope);
    }

    public function servers()
    {
        return $this->hasMany(Server::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
