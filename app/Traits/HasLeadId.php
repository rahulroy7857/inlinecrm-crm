<?php

namespace App\Traits;

use App\Models\Lead;
use App\Models\Source;

trait HasLeadId
{
    public static function generateLeadId($sourceId)
    {
        $source = Source::findOrFail($sourceId);

        // Find last number used across all leads this year
        $lastNumber = Lead::withTrashed()
            ->whereYear('created_at', now()->year)
            ->where('lead_id', 'regexp', '[A-Z]{2}[0-9]{7}')
            ->get()
            ->map(function($lead) {
                return (int)substr($lead->lead_id, -7);
            })
            ->max() ?? 0;

        // Generate next number
        $nextId = $lastNumber + 1;
        
        // Create lead ID with source prefix
        return $source->code . str_pad($nextId, 7, '0', STR_PAD_LEFT);
    }

    protected static function bootHasLeadId()
    {
        static::creating(function ($model) {
            if (!$model->lead_id) {
                $model->lead_id = static::generateLeadId($model->source_id);
            }
        });
    }
}