<?php

namespace App\Models;

use App\Jobs\Services\RunStep;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Step extends Model
{
    protected $guarded = [];

    protected $appends = [
        'logs_excerpt',
        'error_logs_excerpt',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
            'secrets' => 'encrypted:array',
        ];
    }

    public function deployment(): BelongsTo
    {
        return $this->belongsTo(Deployment::class);
    }

    public function logsExcerpt(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->logs ? Str::afterLast($this->logs, "\n"): null,
        );
    }

    public function errorLogsExcerpt(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->error_logs ? Str::afterLast($this->error_logs, "\n"): null,
        );
    }

    public function dispatchJob(): void
    {
        dispatch(new RunStep($this));
    }
}
