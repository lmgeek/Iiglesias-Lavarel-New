<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('church_config', function (Blueprint $table) {
            $table->id();
            $table->string('church_name')->default('Catedral Cristiana');
            $table->text('logo')->nullable();
            $table->text('favicon')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('church_config');
    }
};
