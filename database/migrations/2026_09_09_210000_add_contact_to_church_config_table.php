<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('church_config', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('favicon');
            $table->string('email')->nullable()->after('phone');
            $table->string('instagram')->nullable()->after('email');
            $table->string('facebook')->nullable()->after('instagram');
            $table->string('tiktok')->nullable()->after('facebook');
            $table->string('youtube')->nullable()->after('tiktok');
        });
    }

    public function down(): void
    {
        Schema::table('church_config', function (Blueprint $table) {
            $table->dropColumn(['phone', 'email', 'instagram', 'facebook', 'tiktok', 'youtube']);
        });
    }
};
