<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        // Ambil cart milik user yang sedang login
        $carts = Cart::where('user_id', Auth::id())->get();
        return response()->json(['data' => $carts]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|string',
            'jumlah' => 'required|integer|min:1',
        ]);

        // Cek apakah produk Evomi tersebut sudah ada di cart user
        $cart = Cart::where('user_id', Auth::id())
                    ->where('product_id', $request->product_id)
                    ->first();

        if ($cart) {
            // Jika ada, update jumlahnya
            $cart->jumlah += $request->jumlah;
            $cart->save();
        } else {
            // Jika belum, buat baru
            $cart = Cart::create([
                'user_id' => Auth::id(),
                'product_id' => $request->product_id,
                'jumlah' => $request->jumlah,
            ]);
        }

        return response()->json(['message' => 'Berhasil ditambahkan ke keranjang', 'data' => $cart]);
    }

    public function destroy($id)
    {
        $cart = Cart::where('user_id', Auth::id())->where('_id', $id)->firstOrFail();
        $cart->delete();

        return response()->json(['message' => 'Item berhasil dihapus dari keranjang']);
    }
}