<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category_id',
        'price',
        'data',
        'amount',
        'api_product_id',
        'description',
        'stock',

    ];
    public function orders()
{
    return $this->hasMany(Order::class);
}

public function category()
{
    return $this->belongsTo(Category::class);
}

public function productStocks()
{
    return $this->hasMany(ProductStock::class);
}
}
