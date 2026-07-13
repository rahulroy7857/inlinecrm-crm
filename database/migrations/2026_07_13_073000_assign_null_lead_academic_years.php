<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Assign orphaned leads (NULL academic_year_id) to a valid year
     * so they appear in academic-year-scoped lists and counts.
     */
    public function up(): void
    {
        $fallbackYearId = DB::table('academic_years')
            ->where('name', '2025-26')
            ->value('id')
            ?? DB::table('academic_years')->orderBy('id')->value('id');

        if (!$fallbackYearId) {
            return;
        }

        DB::table('leads')
            ->whereNull('academic_year_id')
            ->update([
                'academic_year_id' => $fallbackYearId,
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        // Irreversible data repair.
    }
};
