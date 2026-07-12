<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lead_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->onDelete('cascade');
            $table->foreignId('from_counselor_id')->nullable()->constrained('counselors')->onDelete('set null');
            $table->foreignId('to_counselor_id')->constrained('counselors')->onDelete('cascade');
            $table->text('note')->nullable();
            $table->string('transferred_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_transfers');
    }
};
