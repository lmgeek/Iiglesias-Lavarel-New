<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->nullable()->unique();
            $table->string('fullname');
            $table->string('born_date')->nullable();
            $table->string('sex', 2)->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('phone')->nullable();
            $table->string('church')->nullable();
            $table->string('mentor')->nullable();
            $table->string('ministerial_range')->nullable();
            $table->unsignedBigInteger('celula')->nullable();
            $table->string('doc_number')->nullable()->unique();
            $table->enum('lider_celula', ['Si', 'No'])->default('No');
            $table->string('password')->nullable();
            $table->string('google_id')->nullable()->unique();
            $table->string('remember_token', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('must_change_password')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index('email');
            $table->index('doc_number');
            $table->index('google_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
