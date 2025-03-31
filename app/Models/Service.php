<?php

namespace App\Models;

use App\Drivers\Driver;
use App\Enums\ServiceCategory;
use App\Enums\ServiceStatus;
use App\Enums\ServiceType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Service extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'status' => ServiceStatus::class,
            'category' => ServiceCategory::class,
            'type' => ServiceType::class,
        ];
    }

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    public function slices(): HasMany
    {
        return $this->hasMany(Slice::class);
    }

    public function deployments(): MorphMany
    {
        return $this->morphMany(Deployment::class, 'target');
    }

    public function driver(
        ?string $defaultPassword = null,
    ): Driver
    {
        $class = config("keystone.drivers.{$this->driver_name}.{$this->version}");
        if (!class_exists($class)) {
            throw new \Exception("Driver class {$class} not found");
        }
        return new $class($this->container_name, $this->container_id, defaultPassword: $defaultPassword);
    }
}
