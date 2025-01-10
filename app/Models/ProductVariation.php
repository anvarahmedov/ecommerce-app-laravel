<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    protected $casts = [
        'variation_type_options_ids' => 'json',
    ];

    protected $fillable = [
        'quantity',
        'price'
    ];

    public function variation_type_options() {
        return $this->belongsToMany(VariationTypeOption::class, 'product_variation_variation_type_option', 'product_variation_id', 'variation_type_option_id');
    }

}
