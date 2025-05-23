<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'thumbnail_url')) {
                $table->string('thumbnail_url')->nullable()->change();
            }
        });
    }

    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'thumbnail_url')) {
                $table->string('thumbnail_url')->change();
            }
        });
    }
}; 