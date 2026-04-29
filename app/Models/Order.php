<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Order extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'orders';

    protected $fillable = [
        'user_id',
        'total_harga',
        'ongkos_kirim',
        'status_pembayaran',
        'alamat_pengiriman',
        'catatan_pengiriman',
        'kurir',
        'order_details', // Array of embedded documents
    ];

    // Cast order_details sebagai array agar otomatis di-handle oleh Laravel
    protected $casts = [
        'order_details' => 'array',
        'total_harga' => 'decimal:2',
        'ongkos_kirim' => 'decimal:2',
    ];
}