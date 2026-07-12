<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('counselors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('mobile')->unique();
            $table->string('password');
            $table->json('languages')->nullable();
            $table->boolean('status')->default(1);
            $table->date('joining_date')->nullable();
            $table->time('office_start_time')->nullable();
            $table->time('office_end_time')->nullable();
            $table->json('working_days')->nullable();
            $table->decimal('salary', 12, 2)->default(0);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('counselors');
    }
};