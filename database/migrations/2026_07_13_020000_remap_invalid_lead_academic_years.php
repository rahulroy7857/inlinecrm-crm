<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Remap leads pointing at a deleted/non-existent academic year (id 3)
     * onto the active academic year so follow-up counts match task lists.
     */
    public function up(): void
    {
        $validYearIds = DB::table('academic_years')->pluck('id');

        if ($validYearIds->isEmpty()) {
            return;
        }

        $activeYearId = DB::table('academic_years')
            ->where('is_active', true)
            ->value('id')
            ?? $validYearIds->sortDesc()->first();

        DB::table('leads')
            ->whereNotNull('academic_year_id')
            ->whereNotIn('academic_year_id', $validYearIds->all())
            ->update([
                'academic_year_id' => $activeYearId,
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        // Irreversible data repair — original invalid year IDs are unknown.
    }
};
