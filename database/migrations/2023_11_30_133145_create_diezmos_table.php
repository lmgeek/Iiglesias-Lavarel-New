<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diezmos', function (Blueprint $table) {
            $table->id();
            $table->uuid('pastor_id')->nullable();
            $table->enum('month', ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']);
            $table->year('year');
            $table->enum('type', ['personal', 'ministerial']);
            $table->timestamps();

            $table->foreign('pastor_id')->references('id')->on('pastors')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diezmos');
    }
};
