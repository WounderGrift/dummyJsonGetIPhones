<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'product';

    protected $fillable = [
        'title',
        'description',
        'category',
        'price',
        'rating',
        'tags',
        'brand',
        'images',
        'thumbnail',
    ];
}
