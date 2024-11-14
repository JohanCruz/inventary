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

        // Renombrar la tabla order_items a order_item
        Schema::rename('order_items', 'order_item');

        
    }

    public function down()
    {
        
        // Renombrar la tabla order_item a order_items
        Schema::rename('order_item', 'order_items');

        
    }
};
