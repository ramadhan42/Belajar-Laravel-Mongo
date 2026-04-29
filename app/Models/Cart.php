<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Cart extends Model
{
    // Tentukan koneksi jika menggunakan multiple database, 
    // jika defaultnya sudah mongodb di .env, baris ini opsional
    protected $connection = 'mongodb'; 
    protected $collection = 'carts';

    protected $fillable = [
        'user_id',
        'product_id',
        'jumlah',
    ];

    // Relasi opsional jika User masih di MySQL atau sudah di MongoDB
    // public function product() { return $this->belongsTo(Product::class); }
}