<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    // GET ALL USERS
    public function index()
    {
        $data = User::all();

        return response()->json($data, 200);
    }

    // GET USER BY ID
    public function show($id)
    {
        $user = User::find($id);
        if (! $user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        return response()->json($user, 200);
    }

    // ✅ POST + UPLOAD PROFILE IMAGE (Sesuai pola ParfumController)
    public function store(Request $request)
    {
        // Di dalam public function store(Request $request)

        $request->validate([
            'name' => 'required|string|max:255',
            // Tambahkan nama koneksi 'mongodb.' sebelum nama collection
            'username' => 'required|string|unique:mongodb.users,username|max:100',
            'email' => 'required|string|email|unique:mongodb.users,email|max:255',
            'password' => 'required|string|min:8',
            'image' => 'sometimes|nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $imageUrl = null;

        // 👉 Handle upload gambar profil seperti pada ParfumController
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('profiles', 'public');
            $imageUrl = asset('storage/'.$path);
        }

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Enkripsi password
            'image' => $imageUrl,
        ]);

        return response()->json([
            'message' => 'User berhasil dibuat',
            'data' => $user,
        ], 201);
    }

    // ✅ UPDATE USER + HANDLE IMAGE (Sesuai pola ParfumController)
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (! $user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        // Validasi opsional untuk update
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'username' => 'sometimes|string|unique:users,username,'.$id,
            'email' => 'sometimes|string|email|unique:users,email,'.$id,
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $dataToUpdate = $request->only(['name', 'username', 'email']);

        // Jika ada password baru
        if ($request->filled('password')) {
            $dataToUpdate['password'] = Hash::make($request->password);
        }

        // 👉 Handle update gambar profil
        if ($request->hasFile('image')) {
            // Hapus foto lama jika ada (optional)
            if ($user->image) {
                $oldPath = str_replace(asset('storage/'), '', $user->image);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('image')->store('profiles', 'public');
            $dataToUpdate['image'] = asset('storage/'.$path);
        }
        $user->update($dataToUpdate);

        return response()->json([
            'message' => 'Profil user berhasil diperbarui',
            'data' => $user,
        ], 200);
    }

    // DELETE USER
    public function destroy($id)
    {
        $user = User::find($id);

        if (! $user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        // Hapus file gambar dari storage sebelum menghapus data user
        if ($user->image) {
            $path = str_replace(asset('storage/'), '', $user->image);
            Storage::disk('public')->delete($path);
        }

        $user->delete();

        return response()->json([
            'message' => 'User berhasil dihapus',
        ], 200);
    }
}
