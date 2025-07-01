<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all(); // Mengambil semua data user dari database
        // dd(UserResource::collection($users)->toArray(request()));

        return Inertia::render('users', [
            'users' => UserResource::collection($users),
            'success_message' => session('success'),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => ['required', Rule::in(['superadmin', 'admin', 'board'])],
        ]);

        User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // Redirect ke rute 'users.index' (yang Anda definisikan nanti)
        // Ini akan menyebabkan Inertia memuat ulang halaman dengan data terbaru
        // dan flash message 'success' akan tersedia di props.
        return redirect()->route('users.index')->with('success', 'User has been added successfully!');
    }
}