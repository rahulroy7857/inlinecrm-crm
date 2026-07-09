<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lead_contact_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('lead_contact_logs', 'contacted_by')) {
                $table->string('contacted_by')->nullable()->after('response_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('lead_contact_logs', function (Blueprint $table) {
            if (Schema::hasColumn('lead_contact_logs', 'contacted_by')) {
                $table->dropColumn('contacted_by');
            }
        });
    }
};
