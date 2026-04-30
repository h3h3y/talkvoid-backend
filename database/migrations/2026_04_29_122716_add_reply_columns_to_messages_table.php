<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable()->after('id');
            $table->string('reply_to_fingerprint')->nullable()->after('sender_fingerprint');
            $table->boolean('is_reply')->default(false)->after('content');

            $table->index('parent_id');
            $table->index('reply_to_fingerprint');
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn(['parent_id', 'reply_to_fingerprint', 'is_reply']);
        });
    }
};
