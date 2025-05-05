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
        Schema::create('mentorship_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('mentorship_programs')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('motivation');
            $table->enum('status', ['applied', 'accepted', 'rejected'])->default('applied');
            $table->timestamps();
            
            // Ensure a user can only apply once to a mentorship program
            $table->unique(['program_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mentorship_applications');
    }
}; 