<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('message_id');
            $table->string('fingerprint'); // Fingerprint pelapor
            $table->string('reason'); // Alasan report
            $table->timestamps();

            // Satu user hanya bisa report satu kali per pesan
            $table->unique(['message_id', 'fingerprint']);

            $table->foreign('message_id')->references('id')->on('messages')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
