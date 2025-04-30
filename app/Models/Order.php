<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'product_id', 
        'quantity', 
        'total_price', 
        'order_details',
        'order_data',  
        'api_order_id', 
         'product_name',
        'status',
    ];

    // If you don't have created_at/updated_at columns, uncomment this line
    // public $timestamps = false;

  // In Order model (app/Models/Order.php)

public function product()
{
    return $this->belongsTo(Product::class); // product_id will reference the Product model
}


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
