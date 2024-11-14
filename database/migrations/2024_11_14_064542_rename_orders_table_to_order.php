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
        // Primero eliminar la restricción de clave foránea
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
        });

        // Renombrar la tabla orders a order
        Schema::rename('orders', 'order');

        // Recrear la restricción de clave foránea
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreign('order_id')->references('id')->on('order');
        });
    }

    public function down()
    {
        // Eliminar la restricción de clave foránea
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
        });

        // Renombrar la tabla order a orders
        Schema::rename('order', 'orders');

        // Recrear la restricción de clave foránea
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreign('order_id')->references('id')->on('orders');
        });
    }
};
