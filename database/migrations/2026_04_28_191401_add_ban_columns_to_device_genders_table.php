<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('device_genders', function (Blueprint $table) {
            $table->boolean('is_banned')->default(false)->after('gender');
            $table->timestamp('banned_until')->nullable()->after('is_banned');
            $table->integer('report_count')->default(0)->after('banned_until');
        });
    }

    public function down(): void
    {
        Schema::table('device_genders', function (Blueprint $table) {
            $table->dropColumn(['is_banned', 'banned_until', 'report_count']);
        });
    }
};
