<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;


class Parfum extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'parfums';
    
    protected $fillable = [
        'nama',
        'brand',
        'price',
        'stock',
        'description',
        'image'
    ];
}
