<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name' , 'is_admin_created'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
    
}
