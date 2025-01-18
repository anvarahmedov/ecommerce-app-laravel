<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    public $timestamps = false;
    protected $fillable = ['order_id', 'product_id', 'quantity', 'price', 'variation_type_options_ids'];

    public function order() {
        return $this->belongsTo(Order::class);
    }

    protected $casts = [
        'variation_type_options_ids' => 'array'
    ];
}
