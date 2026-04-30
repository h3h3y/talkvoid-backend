<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('device_genders', function (Blueprint $table) {
            if (!Schema::hasColumn('device_genders', 'total_reports')) {
                $table->integer('total_reports')->default(0)->after('report_count');
            }
        });
    }

    public function down(): void
    {
        Schema::table('device_genders', function (Blueprint $table) {
            $table->dropColumn('total_reports');
        });
    }
};
