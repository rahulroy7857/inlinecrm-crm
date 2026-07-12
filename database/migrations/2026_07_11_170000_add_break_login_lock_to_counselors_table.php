<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('counselors', function (Blueprint $table) {
            $table->boolean('break_login_locked')->default(false)->after('status');
            $table->timestamp('break_login_locked_at')->nullable()->after('break_login_locked');
            $table->string('break_login_lock_reason')->nullable()->after('break_login_locked_at');
            $table->foreignId('break_login_unlocked_by')->nullable()->after('break_login_lock_reason')->constrained('admins')->nullOnDelete();
            $table->timestamp('break_login_unlocked_at')->nullable()->after('break_login_unlocked_by');
        });

        Schema::table('counselor_breaks', function (Blueprint $table) {
            $table->boolean('exceeded_duration')->default(false)->after('ended_at');
        });
    }

    public function down(): void
    {
        Schema::table('counselor_breaks', function (Blueprint $table) {
            $table->dropColumn('exceeded_duration');
        });

        Schema::table('counselors', function (Blueprint $table) {
            $table->dropConstrainedForeignId('break_login_unlocked_by');
            $table->dropColumn([
                'break_login_locked',
                'break_login_locked_at',
                'break_login_lock_reason',
                'break_login_unlocked_at',
            ]);
        });
    }
};
