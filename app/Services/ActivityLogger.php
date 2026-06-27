<?php

namespace App\Services;

use App\Models\ActivityLog;

class ActivityLogger
{
    public static function log($content, $action, $causer, $properties = [])
    {
        return ActivityLog::create([
            'log_content' => $content,
            'action' => $action,
            'causer_type' => get_class($causer),
            'causer_id' => $causer->id,
            'properties' => $properties
        ]);
    }
}