<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'category')) {
                $table->enum('category', [
                    'web_development',
                    'mobile_development',
                    'design',
                    'database',
                    'programming',
                    'data_science',
                    'artificial_intelligence',
                    'cloud_computing',
                    'cybersecurity',
                    'devops'
                ])->nullable()->after('level');
            }
        });
    }

    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'category')) {
                $table->dropColumn('category');
            }
        });
    }
}; 