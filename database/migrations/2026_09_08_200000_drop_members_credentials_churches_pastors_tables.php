<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Quitar dependencias de la tabla pastors en tablas que se conservan
        Schema::table('diezmos', function (Blueprint $table) {
            $table->dropForeign(['pastor_id']);
            $table->dropColumn('pastor_id');
        });

        Schema::table('intercesion', function (Blueprint $table) {
            $table->dropForeign(['pastor_id']);
            $table->dropColumn('pastor_id');
        });

        // Eliminar tablas (orden: hijas antes que padres por las FKs)
        Schema::dropIfExists('members');
        Schema::dropIfExists('credentials');
        Schema::dropIfExists('churches');
        Schema::dropIfExists('pastors');
    }

    public function down(): void
    {
        // No se restaura: la reversión de tablas eliminadas no se soporta.
    }
};
