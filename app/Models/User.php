<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\OrganisationRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public static function boot()
    {
        parent::boot();

        static::created(function (User $user) {
            $organisation = Organisation::create([
                'name' => $user->name,
                'slug' => Organisation::createUniqueSlug($user->name),
                'owner_id' => $user->id,
            ]);
            $organisation->members()->attach($user, ['role' => OrganisationRole::Admin]);
        });
    }

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function organisations()
    {
        return $this->belongsToMany(Organisation::class)
            ->withPivot('role')
            ->as('membership')
            ->using(OrganisationUser::class)
            ->withTimestamps();
    }
}
