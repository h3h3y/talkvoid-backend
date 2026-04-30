<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('device_genders', function (Blueprint $table) {
            $table->id();
            $table->string('device_id')->unique(); // ID unik perangkat
            $table->string('fingerprint'); // Kombinasi userAgent + screen + timezone
            $table->enum('gender', ['male', 'female']);
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_genders');
    }
};
