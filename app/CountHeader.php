<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountHeader extends Model
{
    use HasFactory;
    protected $fillable = [
        'count_reference',
        'business_id',
        'business_location_id',
        'description',
        'count_type',
        'count_sku_with_zero_stock_onhand',
        'frozen_count',
        'status',
        'categories',
        'sub_categories',
        'brands',
        'final_file',
        'attach_document',
        'count_note',
        'post_note',
        'created_by',
        'user_froze_count',
        'user_posted_count'
    ];

    protected $table = 'count_header';
}