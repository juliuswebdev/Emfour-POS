<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'count_header_id',
        'product_id',
        'sku',
        'upc',
        'frozen_quantity',
        'count_quantity',
        'created_at',
        'updated_at'
    ];

    protected $table = 'count_detail';
}
