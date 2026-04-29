<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product; // Asumsi menggunakan model Product
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function checkout(Request $request)
    {
        $request->validate([
            'alamat_pengiriman' => 'required|string',
            'catatan_pengiriman' => 'nullable|string',
            'kurir' => 'required|string',
        ]);

        $userId = Auth::id();
        $carts = Cart::where('user_id', $userId)->get();

        if ($carts->isEmpty()) {
            return response()->json(['message' => 'Keranjang belanja kosong'], 400);
        }

        $orderDetails = [];
        $totalHarga = 0;
        $ongkosKirim = 12000; // Contoh statis, bisa disesuaikan dengan API kurir

        // Loop keranjang untuk dimasukkan ke array order_details
        foreach ($carts as $cart) {
            // Simulasi fetch harga produk saat ini (sebaiknya diambil dari database Products)
            // $product = Product::where('id', $cart->product_id)->first();
            // $hargaSaatBeli = $product->harga_retail;
            
            // Dummy harga untuk contoh:
            $hargaSaatBeli = 25000; 

            $subTotal = $hargaSaatBeli * $cart->jumlah;
            $totalHarga += $subTotal;

            // Struktur Embedded Document untuk detail
            $orderDetails[] = [
                'product_id' => $cart->product_id,
                'jumlah' => $cart->jumlah,
                'harga_saat_beli' => $hargaSaatBeli,
                'sub_total' => $subTotal,
            ];
        }

        // Buat Order baru dengan data embedded
        $order = Order::create([
            'user_id' => $userId,
            'total_harga' => $totalHarga,
            'ongkos_kirim' => $ongkosKirim,
            'status_pembayaran' => 'pending',
            'alamat_pengiriman' => $request->alamat_pengiriman,
            'catatan_pengiriman' => $request->catatan_pengiriman,
            'kurir' => $request->kurir,
            'order_details' => $orderDetails, // Masukkan array langsung ke MongoDB
        ]);

        // Bersihkan keranjang setelah checkout berhasil
        Cart::where('user_id', $userId)->delete();

        return response()->json([
            'message' => 'Checkout berhasil',
            'data' => $order
        ], 201);
    }

    public function show($id)
    {
        $order = Order::where('user_id', Auth::id())->where('_id', $id)->firstOrFail();
        
        return response()->json(['data' => $order]);
    }
}