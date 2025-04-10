<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Organisation extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    protected $guarded = [];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->as('membership')
            ->using(OrganisationUser::class)
            ->withTimestamps();
    }

    public function servers(): HasMany
    {
        return $this->hasMany(Server::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function providers(): HasMany
    {
        return $this->hasMany(Provider::class);
    }

    public function networks(): HasMany
    {
        return $this->hasMany(Network::class);
    }

    public static function createUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $count = 2;
        while (Organisation::where('slug', $slug)->exists()) {
            $slug = Str::slug($name).'-'.$count++;
        }

        return $slug;
    }
}
