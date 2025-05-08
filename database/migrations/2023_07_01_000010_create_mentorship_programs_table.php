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
        Schema::create('mentorship_programs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->foreignId('mentor_id')->constrained('users')->onDelete('cascade');
            $table->string('duration')->nullable();
            $table->integer('capacity')->nullable();
            $table->enum('status', ['open', 'closed', 'completed'])->default('open');
            $table->enum('category', ['web', 'mobile', 'design', 'database', 'career', 'other'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mentorship_programs');
    }
}; 