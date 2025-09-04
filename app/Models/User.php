<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use App\Notifications\Auth\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Authenticatable implements HasMedia
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean'
        ];
    }

    public function preferences()
    {
        return $this->hasMany(\App\Models\UserPreference::class);
    }

    public function servers()
    {
        return $this->hasMany(\App\Models\Server::class, 'created_by');
    }

    public function sshKeys()
    {
        return $this->hasMany(\App\Models\SshKey::class, 'created_by');
    }

    public function securityGroups()
    {
        return $this->hasMany(\App\Models\SecurityGroup::class, 'created_by');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('user-photo')->singleFile();
    }
    
    // public function profilePhotoUrl(): Attribute
    // {
    //     return Attribute::get(function () {
    //         $this->loadMissing("media");
    //         $media = $this->getFirstMedia("profile-picture");

    //         if ($media && count($media))
    //         {
    //             return $media[0]->getFullUrl();
    //         }

    //         return "/src/img/avatar.png";
    //     });
    // }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
