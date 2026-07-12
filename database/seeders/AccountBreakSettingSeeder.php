<?php

namespace Database\Seeders;

use App\Models\AccountBreak;
use App\Models\AccountBreakSetting;
use Illuminate\Database\Seeder;

class AccountBreakSettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            ['type' => AccountBreak::TYPE_TEA_COFFEE, 'label' => 'Tea / Coffee', 'duration_minutes' => 10, 'requires_admin_approval' => false, 'sort_order' => 1],
            ['type' => AccountBreak::TYPE_LUNCH, 'label' => 'Lunch', 'duration_minutes' => 30, 'requires_admin_approval' => false, 'sort_order' => 2],
            ['type' => AccountBreak::TYPE_SCHOOL_COLLEGE, 'label' => 'School / College Visit', 'duration_minutes' => null, 'requires_admin_approval' => true, 'sort_order' => 3],
            ['type' => AccountBreak::TYPE_STUDENT_MEETING, 'label' => 'Student Meeting', 'duration_minutes' => null, 'requires_admin_approval' => true, 'sort_order' => 4],
            ['type' => AccountBreak::TYPE_MEETING, 'label' => 'Meeting', 'duration_minutes' => null, 'requires_admin_approval' => false, 'sort_order' => 5],
            ['type' => AccountBreak::TYPE_OUTSIDE_WORK, 'label' => 'Outside Work', 'duration_minutes' => null, 'requires_admin_approval' => true, 'sort_order' => 6],
        ];

        foreach ($defaults as $setting) {
            AccountBreakSetting::updateOrCreate(['type' => $setting['type']], $setting);
        }
    }
}
