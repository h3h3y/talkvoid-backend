<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_reports', function (Blueprint $table) {
            $table->id();
            $table->string('reporter_fingerprint'); // Fingerprint pelapor (karena tidak ada user login)
            $table->string('reported_fingerprint'); // Fingerprint terlaporkan
            $table->string('reason');
            $table->timestamps();

            // Cegah report ganda dari fingerprint yang sama
            $table->unique(['reporter_fingerprint', 'reported_fingerprint']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_reports');
    }
};
