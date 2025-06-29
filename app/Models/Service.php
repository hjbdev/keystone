<?php

namespace App\Models;

use App\Drivers\DatabaseDriver;
use App\Drivers\Driver;
use App\Enums\ServiceCategory;
use App\Enums\ServiceStatus;
use App\Enums\ServiceType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Service extends Model
{
    protected $guarded = [];

    protected $hidden = ['credentials', 'container_name', 'container_id'];

    protected function casts(): array
    {
        return [
            'status' => ServiceStatus::class,
            'category' => ServiceCategory::class,
            'type' => ServiceType::class,
            'credentials' => 'encrypted:array',
        ];
    }

    public function folderName(): Attribute
    {
        return new Attribute(
            get: fn() => $this->name . '-' . $this->id,
        );
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

    public function driver(): Driver
    {
        $class = config("keystone.drivers.{$this->driver_name}");
        if (! class_exists($class)) {
            throw new \Exception("Driver class {$class} not found");
        }

        $driver = new $class(
            containerName: $this->container_name,
            containerId: $this->container_id,
            service: $this,
        );

        if ($driver instanceof DatabaseDriver) {
            $driver->credentials = $this->credentials;
        }

        return $driver;
    }
}
