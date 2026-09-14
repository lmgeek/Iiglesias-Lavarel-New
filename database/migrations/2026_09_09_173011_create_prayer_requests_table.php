<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prayer_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name')->nullable();
            $table->text('request');
            $table->boolean('is_answered')->default(false);
            $table->timestamp('answered_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_answered', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prayer_requests');
    }
};
