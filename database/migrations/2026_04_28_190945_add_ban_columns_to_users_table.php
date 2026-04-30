<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_banned')->default(false);
            $table->timestamp('banned_until')->nullable(); // Untuk ban sementara
        });
    }
    // ...
};
