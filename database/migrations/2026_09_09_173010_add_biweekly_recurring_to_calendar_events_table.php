<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('calendar_events', function (Blueprint $table) {
            $table->enum('recurring_frequency', ['daily', 'biweekly', 'weekly', 'monthly', 'yearly'])->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('calendar_events', function (Blueprint $table) {
            $table->enum('recurring_frequency', ['daily', 'weekly', 'monthly', 'yearly'])->nullable()->change();
        });
    }
};
