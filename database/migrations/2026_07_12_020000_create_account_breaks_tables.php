<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_break_settings', function (Blueprint $table) {
            $table->id();
            $table->string('type')->unique();
            $table->string('label');
            $table->unsignedInteger('duration_minutes')->nullable();
            $table->boolean('requires_admin_approval')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('account_breaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('accounts')->cascadeOnDelete();
            $table->string('type');
            $table->string('approval_status')->nullable();
            $table->timestamp('requested_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->string('rejected_reason')->nullable();
            $table->unsignedInteger('duration_minutes')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->boolean('exceeded_duration')->default(false);
            $table->timestamps();

            $table->index(['account_id', 'started_at']);
        });

        Schema::table('accounts', function (Blueprint $table) {
            $table->boolean('break_login_locked')->default(false)->after('status');
            $table->timestamp('break_login_locked_at')->nullable()->after('break_login_locked');
            $table->string('break_login_lock_reason')->nullable()->after('break_login_locked_at');
            $table->foreignId('break_login_unlocked_by')->nullable()->after('break_login_lock_reason')->constrained('admins')->nullOnDelete();
            $table->timestamp('break_login_unlocked_at')->nullable()->after('break_login_unlocked_by');
        });
    }

    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('break_login_unlocked_by');
            $table->dropColumn([
                'break_login_locked',
                'break_login_locked_at',
                'break_login_lock_reason',
                'break_login_unlocked_at',
            ]);
        });

        Schema::dropIfExists('account_breaks');
        Schema::dropIfExists('account_break_settings');
    }
};
