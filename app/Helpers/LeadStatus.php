<?php

namespace App\Helpers;

class LeadStatus
{
    const STATUSES = [
        'New' => [
            'color' => 'purple',
            'hex' => '#6f42c1' // Bootstrap purple
        ],
        'Hot' => [
            'color' => 'danger',
            'hex' => '#dc3545' // Bootstrap danger red
        ],
        'Warm' => [
            'color' => 'warning',
            'hex' => '#ffc107' // Bootstrap warning yellow
        ],
        'Cold' => [
            'color' => 'info',
            'hex' => '#0dcaf0' // Bootstrap info blue
        ],
        'Fake' => [
            'color' => 'dark',
            'hex' => '#212529' // Bootstrap dark
        ],
        'Junk' => [
            'color' => 'secondary',
            'hex' => '#6c757d' // Bootstrap secondary
        ],
        'Application' => [
            'color' => 'primary',
            'hex' => '#0d6efd' // Bootstrap primary blue
        ],
        'Reservation' => [
            'color' => 'indigo',
            'hex' => '#6610f2' // Bootstrap indigo
        ],
        'Admission' => [
            'color' => 'success',
            'hex' => '#198754' // Bootstrap success green
        ],
        'Cancelled' => [
            'color' => 'danger-subtle',
            'hex' => '#f8d7da' // Bootstrap danger subtle
        ]
    ];

    public static function getColor($status)
    {
        return self::STATUSES[$status]['color'] ?? 'secondary';
    }

    public static function getHexColor($status)
    {
        return self::STATUSES[$status]['hex'] ?? '#6c757d'; // Default to secondary color
    }

    public static function getAllStatuses()
    {
        return array_keys(self::STATUSES);
    }

    public static function getBadge($status)
    {
        $color = self::getColor($status);
        return "<span class='badge bg-{$color} badge-status'>{$status}</span>";
    }
}