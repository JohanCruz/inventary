<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{  protected $table = 'order_item';
    
    protected $fillable = [
    "product_id",
    "order_id",
    "quantity_requested",
    "quantity_to_deliver",
    "subtotal",
    "updated_at", 
    "created_at"
    ];
    public function product() {
        return $this->belongsTo(Product::class, 'product_id'); 
    }

    public function order() {
        return $this->belongsTo(Order::class, 'order_id'); 
    }
}
