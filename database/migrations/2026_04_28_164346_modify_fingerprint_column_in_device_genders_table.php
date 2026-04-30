<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('device_genders', function (Blueprint $table) {
            // Ubah tipe kolom fingerprint menjadi TEXT (bisa menampung lebih banyak data)
            $table->text('fingerprint')->change();
            // Ubah device_id juga jika perlu
            $table->string('device_id', 255)->change();
        });
    }

    public function down(): void
    {
        Schema::table('device_genders', function (Blueprint $table) {
            // Kembalikan ke ukuran semula (jika rollback)
            $table->string('fingerprint', 255)->change();
            $table->string('device_id', 255)->change();
        });
    }
};
