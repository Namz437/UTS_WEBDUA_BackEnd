<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddToCart extends Model
{
    use HasFactory;

    public $table = 'add_to_cart';

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
