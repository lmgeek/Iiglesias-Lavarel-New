<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('relationship_members', function (Blueprint $table) {
            $table->foreignId('mentor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('disciple_id')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('f_meet');
            $table->enum('suspended', ['Si', 'No'])->default('No');
            $table->text('why_suspended')->nullable();
            $table->foreignId('theme_meetings_id')->nullable()->constrained('meetings_themes')->onDelete('set null');
            $table->string('other_theme')->nullable();
            $table->string('culminate')->nullable();
            $table->string('initiative')->nullable();
            $table->string('reading')->nullable();
            $table->string('testimonials')->nullable();
            $table->string('pray_together')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('relationship_members');
    }
};
