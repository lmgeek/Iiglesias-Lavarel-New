<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('ministry_id')->nullable()->after('mentor');
            $table->unsignedBigInteger('sede_id')->nullable()->after('ministry_id');
            $table->index('ministry_id');
            $table->index('sede_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['ministry_id']);
            $table->dropIndex(['sede_id']);
            $table->dropColumn(['ministry_id', 'sede_id']);
        });
    }
};
