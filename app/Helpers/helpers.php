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
            'South Africa', 'Italy', 'Spain', 'Netherlands', 'Sweden', 'Norway', 'Finland', 'Denmark',
        ];
    }
}

if (!function_exists('country_codes')) {
    function country_codes()
    {
        return [
            'India' => '+91',
            'USA' => '+1',
            'UK' => '+44',
            'Canada' => '+1',
            'Australia' => '+61',
            'Germany' => '+49',
            'France' => '+33',
            'Japan' => '+81',
            'China' => '+86',
            'Brazil' => '+55',
            'South Africa' => '+27',
            'Italy' => '+39',
            'Spain' => '+34',
            'Netherlands' => '+31',
            'Sweden' => '+46',
            'Norway' => '+47',
            'Finland' => '+358',
            'Denmark' => '+45',
           
        ];
    }
}

if (!function_exists('lead_phone_prefix')) {
    function lead_phone_prefix(?string $country): string
    {
        if (!$country) {
            return '';
        }

        $code = country_codes()[$country] ?? '';

        return $code ? $code . ' - ' : '';
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