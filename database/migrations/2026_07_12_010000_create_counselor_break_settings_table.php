<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('counselor_break_settings', function (Blueprint $table) {
            $table->id();
            $table->string('type')->unique();
            $table->string('label');
            $table->unsignedInteger('duration_minutes')->nullable();
            $table->boolean('requires_admin_approval')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('counselor_break_settings');
    }
};
