<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports_celula', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentor_id')->constrained('users')->onDelete('cascade');
            $table->dateTime('f_meet');
            $table->string('celula');
            $table->enum('suspended', ['Si', 'No'])->default('No');
            $table->text('why_suspended')->nullable();
            $table->string('lider');
            $table->string('message_title')->nullable();
            $table->string('who_meet')->nullable();
            $table->string('format')->nullable();
            $table->integer('people_qty')->nullable();
            $table->integer('new_people_qty')->nullable();
            $table->string('mentoring')->nullable();
            $table->text('observations')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('f_meet');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports_celula');
    }
};
