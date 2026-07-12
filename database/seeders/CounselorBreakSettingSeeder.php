<?php

namespace Database\Seeders;

use App\Models\CounselorBreak;
use App\Models\CounselorBreakSetting;
use Illuminate\Database\Seeder;

class CounselorBreakSettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            [
                'type' => CounselorBreak::TYPE_TEA_COFFEE,
                'label' => 'Tea / Coffee',
                'duration_minutes' => 10,
                'requires_admin_approval' => false,
                'sort_order' => 1,
            ],
            [
                'type' => CounselorBreak::TYPE_LUNCH,
                'label' => 'Lunch',
                'duration_minutes' => 30,
                'requires_admin_approval' => false,
                'sort_order' => 2,
            ],
            [
                'type' => CounselorBreak::TYPE_SCHOOL_COLLEGE,
                'label' => 'School / College Visit',
                'duration_minutes' => null,
                'requires_admin_approval' => true,
                'sort_order' => 3,
            ],
            [
                'type' => CounselorBreak::TYPE_STUDENT_MEETING,
                'label' => 'Student Meeting',
                'duration_minutes' => null,
                'requires_admin_approval' => true,
                'sort_order' => 4,
            ],
            [
                'type' => CounselorBreak::TYPE_MEETING,
                'label' => 'Meeting',
                'duration_minutes' => null,
                'requires_admin_approval' => false,
                'sort_order' => 5,
            ],
            [
                'type' => CounselorBreak::TYPE_OUTSIDE_WORK,
                'label' => 'Outside Work',
                'duration_minutes' => null,
                'requires_admin_approval' => true,
                'sort_order' => 6,
            ],
        ];

        foreach ($defaults as $setting) {
            CounselorBreakSetting::updateOrCreate(
                ['type' => $setting['type']],
                $setting
            );
        }
    }
}
