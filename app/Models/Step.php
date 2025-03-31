<?php

namespace App\Models;

use App\Jobs\Services\RunStep;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Step extends Model
{
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

    public function dispatchJob(): void
    {
        dispatch(new RunStep($this));
    }
}
