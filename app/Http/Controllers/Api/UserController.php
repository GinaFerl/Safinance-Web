<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
     public function index()
    {
        $users = UserResource::collection(User::all());
        return response()->json([
            'message' => 'Users retrieved successfully',
            'data' => $users
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'profileImage' => 'nullable|string|max:255',
            'role' => ['required', Rule::in(['superadmin', 'admin', 'board'])],
        ]);

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'profileImage' => $request->profileImage,
            'role' => $request->role,
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'data' => $user
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json([
            'message' => 'User retrieved successfully',
            'data' => $user
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $request->validate([
            'username' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => 'nullable|string|min:8',
            'profileImage' => 'nullable|string|max:255',
            'role' => ['sometimes', 'required', Rule::in(['superadmin', 'admin', 'board'])],
        ]);

        $user->username = $request->username ?? $user->username;
        $user->email = $request->email ?? $user->email;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->profileImage = $request->profileImage ?? $user->profileImage;
        $user->role = $request->role ?? $user->role;
        $user->save();

        return response()->json([
            'message' => 'User updated successfully',
            'data' => $user
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        /** @var \App\Models\User|null $user */ // <-- Tambahkan baris ini
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete(); // Peringatan seharusnya hilang di sini

        return response()->json([
            'message' => 'User deleted successfully'
        ], 200);
    }

    /** 
     * Changes password
    */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed', // 'confirmed' berarti harus ada new_password_confirmation
        ]);

        $user = $request->user(); // Mendapatkan user yang sedang terautentikasi

        // Verifikasi password saat ini
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Password saat ini tidak cocok.'
            ], 400); // Bad Request
        }

        // Update password baru
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'message' => 'Password berhasil diubah.'
        ], 200);
    }
}
