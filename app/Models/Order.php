<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{   protected $table = 'order';
    protected $fillable = [
        "id",
        "status",
        "updated_at", 
        "created_at",
    ];
    public function items() {
        return $this->hasMany(OrderItem::class, 'order_id');
    }
}
