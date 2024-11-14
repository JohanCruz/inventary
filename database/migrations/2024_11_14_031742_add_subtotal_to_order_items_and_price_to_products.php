<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Agregar la columna `subtotal` a la tabla `order_items`
        Schema::table('order_items', function (Blueprint $table) {
            $table->integer('subtotal')->default(0)->after('quantity_to_deliver');
        });

        // Agregar la columna `price` a la tabla `products`
        Schema::table('product', function (Blueprint $table) {
            $table->integer('price')->default(0)->after('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar la columna `subtotal` de la tabla `order_items`
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('subtotal');
        });

        // Eliminar la columna `price` de la tabla `products`
        Schema::table('product', function (Blueprint $table) {
            $table->dropColumn('price');
        });
    }
};
