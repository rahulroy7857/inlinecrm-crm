<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('counselor_breaks', function (Blueprint $table) {
            $table->string('approval_status')->nullable()->after('type');
            $table->timestamp('requested_at')->nullable()->after('approval_status');
            $table->foreignId('approved_by')->nullable()->after('requested_at')->constrained('admins')->nullOnDelete();
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->string('rejected_reason')->nullable()->after('approved_at');
        });

        Schema::table('counselor_breaks', function (Blueprint $table) {
            $table->timestamp('started_at')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('counselor_breaks', function (Blueprint $table) {
            $table->timestamp('started_at')->nullable(false)->change();
            $table->dropConstrainedForeignId('approved_by');
            $table->dropColumn([
                'approval_status',
                'requested_at',
                'approved_at',
                'rejected_reason',
            ]);
        });
    }
};
