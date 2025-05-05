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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->text('long_description');
            $table->string('thumbnail_url');
            $table->enum('level', ['beginner', 'intermediate', 'advanced']);
            $table->string('duration');
            $table->integer('total_videos')->default(0);
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('rating', 2, 1)->default(0);
            $table->integer('rating_count')->default(0);
            $table->integer('students_count')->default(0);
            $table->string('last_update');
            $table->json('requirements')->nullable();
            $table->json('what_you_will_learn')->nullable();
            $table->foreignId('instructor_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
}; 