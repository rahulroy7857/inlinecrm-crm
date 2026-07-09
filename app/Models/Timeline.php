<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Timeline extends Model
{
    protected $fillable = [
        'lead_id',
        'title',
        'description',
        'performer_type',
        'performer_id',
        'event_type',
        'event_date',
    ];

    protected $dates = [
        'event_date',
    ];

    public static function performerAttributes(?Authenticatable $actor = null): array
    {
        $actor ??= auth()->user();

        if (!$actor) {
            return [
                'performer_type' => null,
                'performer_id' => null,
            ];
        }

        return [
            'performer_type' => $actor->getMorphClass(),
            'performer_id' => $actor->getAuthIdentifier(),
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function performer(): MorphTo
    {
        return $this->morphTo();
    }
}
