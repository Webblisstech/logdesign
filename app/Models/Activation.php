<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activation extends Model
{
    use HasFactory;

    protected $fillable = [
        'activation_id', 'phone_number', 'service', 'status', 'code', 'user_email', 'cost', 'source', 'tellabot_id'
    ];

    public function user()
{
    return $this->belongsTo(User::class);
}

}
