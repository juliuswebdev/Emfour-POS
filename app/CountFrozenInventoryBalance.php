<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountFrozenInventoryBalance extends Model
{
    use HasFactory;
    protected $fillable = [
        'count_header_id',
        'product_id',
        'sku',
        'upc',
        'frozen_quantity',
        'created_at',
        'updated_at'
    ];

    protected $table = 'count_frozen_inventory_balance';
}
