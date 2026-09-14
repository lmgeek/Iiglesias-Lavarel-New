<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('intercesion', function (Blueprint $table) {
            $table->id();
            $table->uuid('pastor_id')->nullable();
            $table->integer('calendar_day');
            $table->string('email');
            $table->boolean('notifications')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('pastor_id')->references('id')->on('pastors')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('intercesion');
    }
};
