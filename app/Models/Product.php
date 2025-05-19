<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Specify the correct table name
    protected $table = 'products';

    protected $fillable = [
        'name',
        'description',
        'price',
        'image',
        'featured'
    ];

    // Cast price to float
    protected $casts = [
        'price' => 'float',
        'featured' => 'boolean',
    ];
}
