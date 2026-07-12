<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('counselor_breaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('counselor_id')->constrained('counselors')->cascadeOnDelete();
            $table->string('type');
            $table->unsignedInteger('duration_minutes')->nullable();
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();

            $table->index(['counselor_id', 'started_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('counselor_breaks');
    }
};
