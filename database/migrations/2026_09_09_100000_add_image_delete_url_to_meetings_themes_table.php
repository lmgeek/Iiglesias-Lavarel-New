<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('meetings_themes', function (Blueprint $table) {
            if (! Schema::hasColumn('meetings_themes', 'image_delete_url')) {
                $table->string('image_delete_url')->nullable()->after('image');
            }
        });
    }

    public function down(): void
    {
        Schema::table('meetings_themes', function (Blueprint $table) {
            if (Schema::hasColumn('meetings_themes', 'image_delete_url')) {
                $table->dropColumn('image_delete_url');
            }
        });
    }
};
