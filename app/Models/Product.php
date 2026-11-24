<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    public $timestamps = false;

    protected $guarded = [];

    // Cố ý KHÔNG có $casts: để thấy đúng thứ DB nhận được
}
