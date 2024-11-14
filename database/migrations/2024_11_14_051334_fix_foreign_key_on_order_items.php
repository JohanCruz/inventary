<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Eliminar la clave foránea existente que apunta a 'products'
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']); // Elimina la clave foránea incorrecta
        });

        // Agregar la clave foránea correcta que apunta a 'product'
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('product'); // Cambiar 'products' por 'product'
        });
    }

    public function down()
    {
        // En caso de que necesites revertir la migración
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->foreign('product_id')->references('id')->on('products'); // Regresar a 'products' si es necesario
        });
    }
};
