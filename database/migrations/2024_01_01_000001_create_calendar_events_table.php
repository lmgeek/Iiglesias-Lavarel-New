<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calendar_events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->enum('recurring_frequency', ['daily', 'weekly', 'monthly', 'yearly'])->nullable();
            $table->date('recurring_end_date')->nullable();
            $table->json('recurring_days')->nullable(); // Array de números 0-6 (domingo-sábado)
            $table->string('color', 7)->default('#c57125');
            $table->boolean('all_day')->default(false);
            $table->string('location')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['start_date', 'end_date']);
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendar_events');
    }
};
