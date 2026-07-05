<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('counselors', function (Blueprint $table) {
            $table->date('joining_date')->nullable()->after('status');
            $table->time('office_start_time')->nullable()->after('joining_date');
            $table->time('office_end_time')->nullable()->after('office_start_time');
            $table->json('working_days')->nullable()->after('office_end_time');
            $table->decimal('salary', 12, 2)->default(0)->after('working_days');
        });
    }

    public function down(): void
    {
        Schema::table('counselors', function (Blueprint $table) {
            $table->dropColumn([
                'joining_date',
                'office_start_time',
                'office_end_time',
                'working_days',
                'salary',
            ]);
        });
    }
};
