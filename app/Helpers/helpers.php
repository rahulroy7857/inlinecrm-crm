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
            'india' => '+91',
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

if (!function_exists('is_admin_account_portal')) {
    function is_admin_account_portal(): bool
    {
        return request()->is('admin/accounts*');
    }
}

if (!function_exists('account_route_prefix')) {
    function account_route_prefix(): string
    {
        return is_admin_account_portal() ? 'admin.accounts' : 'account';
    }
}

if (!function_exists('account_route')) {
    function account_route(string $name, $parameters = [], bool $absolute = true): string
    {
        return route(account_route_prefix() . '.' . $name, $parameters, $absolute);
    }
}

if (!function_exists('account_can_manage')) {
    function account_can_manage(): bool
    {
        if (is_admin_account_portal()) {
            return auth()->guard('admin')->check();
        }

        return auth()->guard('account')->user()?->canManage() ?? false;
    }
}

if (!function_exists('account_actor')) {
    function account_actor()
    {
        if (is_admin_account_portal()) {
            return auth()->guard('admin')->user();
        }

        return auth()->guard('account')->user();
    }
}

if (!function_exists('account_user_name')) {
    function account_user_name(): string
    {
        return account_actor()?->name ?? '';
    }
}

if (!function_exists('indian_states')) {
    function indian_states(): array
    {
        return [
            'Any', 'Andhra Pradesh', 'Arunachal Pradesh', 'Assam', 'Bihar', 'Chhattisgarh', 'Goa', 'Gujarat',
            'Haryana', 'Himachal Pradesh', 'Jharkhand', 'Karnataka', 'Kerala', 'Madhya Pradesh',
            'Maharashtra', 'Manipur', 'Meghalaya', 'Mizoram', 'Nagaland', 'Odisha', 'Punjab',
            'Rajasthan', 'Sikkim', 'Tamil Nadu', 'Telangana', 'Tripura', 'Uttar Pradesh',
            'Uttarakhand', 'West Bengal',
        ];
    }
}

if (!function_exists('parse_editable_date')) {
    function parse_editable_date(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        $formats = ['d-m-Y', 'd-m-y', 'Y-m-d', 'Y-n-j', 'd/m/Y', 'd-m-Y H:i:s'];

        foreach ($formats as $format) {
            try {
                return \Carbon\Carbon::createFromFormat($format, $value)->format('Y-m-d');
            } catch (\Exception $e) {
                continue;
            }
        }

        return \Carbon\Carbon::parse($value)->format('Y-m-d');
    }
}

if (!function_exists('student_registration_url')) {
    function student_registration_url(?string $leadRef): string
    {
        if (!$leadRef) {
            return route('student.registration');
        }

        return route('student.registration.lead', $leadRef);
    }
}