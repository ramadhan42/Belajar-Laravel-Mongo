<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Parfum;
use MongoDB\BSON\ObjectId;

class ParfumController extends Controller
{
    // GET ALL
    public function index()
    {
        $data = Parfum::all();
        return response()->json($data, 200);
    }

    // GET BY ID
    public function show($id)
    {
        $parfum = Parfum::find($id);

        if (!$parfum) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json($parfum, 200);
    }

    // ✅ POST + UPLOAD IMAGE
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string',
            'description' => 'string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $kategori = $request->kategori_produk;

        // 👉 handle upload gambar
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('parfum', 'public');
            $imageUrl = asset('storage/' . $path);

            // simpan ke produk pertama (contoh)
            $kategori[0]['media']['image_url'] = $imageUrl;
        }

        $data = Parfum::create([
            'nama' => $request->nama,
            'brand' => $request->brand,
            'price' => $request->price,
            'stock' => $request->stock,
            'description' => $request->description,
            'image' => $imageUrl ?? null,
        ]);

        return response()->json([
            'message' => 'Data + gambar berhasil ditambahkan',
            'data' => $data
        ], 201);
    }

    // ✅ UPDATE + GANTI GAMBAR
    public function update(Request $request, $id)
    {
        $parfum = Parfum::where('_id', new ObjectId($id))->first();

        if (!$parfum) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $parfum->update([
            'nama' => $request->nama ?? $parfum->nama,
            'brand' => $request->brand ?? $parfum->brand,
            'price' => $request->price ?? $parfum->price,
            'stock' => $request->stock ?? $parfum->stock,
            'description' => $request->description ?? $parfum->description,
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = $file->store('parfum', 'public');
            $parfum->image = asset('storage/' . $path);
            $parfum->save();
        }

        return response()->json([
            'message' => 'Data berhasil diupdate',
            'data' => $parfum->fresh()
        ]);
    }

    public function uploadPerProduk(Request $request, $parfum_id, $produk_id)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $parfum = Parfum::find($parfum_id);

        if (!$parfum) {
            return response()->json([
                'message' => 'Data parfum tidak ditemukan'
            ], 404);
        }

        $kategori = $parfum->kategori_produk;

        $found = false;

        foreach ($kategori as $index => $produk) {

            if ($produk['id'] === $produk_id) {

                // ✅ Hapus gambar lama (optional tapi penting)
                if (isset($produk['media']['image_url'])) {
                    $oldPath = str_replace(asset('storage') . '/', '', $produk['media']['image_url']);
                    Storage::disk('public')->delete($oldPath);
                }

                // ✅ Upload gambar baru
                $path = $request->file('image')->store('parfum', 'public');
                $imageUrl = asset('storage/' . $path);

                // ✅ Update hanya produk ini
                $kategori[$index]['media']['image_url'] = $imageUrl;

                $found = true;
                break;
            }
        }

        if (!$found) {
            return response()->json([
                'message' => 'Produk tidak ditemukan (ID salah)'
            ], 404);
        }

        // ✅ Simpan kembali ke MongoDB
        $parfum->update([
            'kategori_produk' => $kategori
        ]);

        return response()->json([
            'message' => 'Gambar berhasil diupload untuk produk ' . $produk_id,
            'data' => $parfum
        ], 200);
    }

    public function updateProduk(Request $request, $parfum_id, $produk_id)
    {
        $parfum = Parfum::find($parfum_id);

        if (!$parfum) {
            return response()->json([
                'message' => 'Data parfum tidak ditemukan'
            ], 404);
        }

        $kategori = $parfum->kategori_produk;
        $found = false;

        foreach ($kategori as $index => $produk) {

            if ($produk['id'] === $produk_id) {

                // ✅ Update field yang dikirim saja (tidak overwrite semua)
                $kategori[$index]['nama'] = $request->nama ?? $produk['nama'];
                $kategori[$index]['deskripsi'] = $request->deskripsi ?? $produk['deskripsi'];

                // nested update (optional)
                if ($request->has('spesifikasi')) {
                    $kategori[$index]['spesifikasi'] = array_merge(
                        $produk['spesifikasi'] ?? [],
                        $request->spesifikasi
                    );
                }

                if ($request->has('transaksi')) {
                    $kategori[$index]['transaksi'] = array_merge(
                        $produk['transaksi'] ?? [],
                        $request->transaksi
                    );
                }

                $found = true;
                break;
            }
        }

        if (!$found) {
            return response()->json([
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }

        // ✅ Simpan ke MongoDB
        $parfum->update([
            'kategori_produk' => $kategori
        ]);

        return response()->json([
            'message' => 'Produk berhasil diupdate',
            'data' => $parfum
        ], 200);
    }

    // DELETE
    public function destroy($id)
    {
        $parfum = Parfum::find($id);

        if (!$parfum) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $parfum->delete();

        return response()->json([
            'message' => 'Data berhasil dihapus'
        ], 200);
    }
}
