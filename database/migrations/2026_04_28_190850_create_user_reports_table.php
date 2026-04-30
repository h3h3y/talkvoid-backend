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
            $table->foreignId('reporter_id')->constrained('users'); // Yang melapor
            $table->foreignId('reported_user_id')->constrained('users'); // Yang dilapor
            $table->string('reason');
            $table->timestamps();

            // Cegah report ganda dari user yang sama
            $table->unique(['reporter_id', 'reported_user_id']);
        });
    }
};
