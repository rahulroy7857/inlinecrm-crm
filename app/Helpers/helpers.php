<?php

if (!function_exists('current_academic_year')) {
    function current_academic_year()
    {
        $academicYearId = session('academic_year_id');
        return \App\Models\AcademicYear::find($academicYearId);
    }
}

if (!function_exists('academic_years')) {
    function academic_years()
    {
        return \App\Models\AcademicYear::all();
    }
}

if (!function_exists('countries')) {
    function countries()
    {
        return [
            'India', 'USA', 'UK', 'Canada', 'Australia', 'Germany', 'France', 'Japan', 'China', 'Brazil',
            'South Africa', 'Italy', 'Spain', 'Netherlands', 'Sweden', 'Norway', 'Finland', 'Denmark'
        ];
    }
}

if (!function_exists('transaction_types')) {
    function transaction_types($type = null)
    {
        $types = [
            1 => 'Received From Student',
            2 => 'Received From Agent',
            3 => 'Received From College',
            4 => 'Paid To Student',
            5 => 'Paid To Agent',
            6 => 'Paid To College',
            7 => 'Other'
        ];
        return $type ? $types[$type] ?? null : $types;
    }
}