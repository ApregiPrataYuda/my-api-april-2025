<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\users;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\RegistrationValidated;

class Auth extends Controller
{
    public function register(RegistrationValidated $request)
    {
        try {
            $user = users::create([
                'fullname' => $request->fullname,
                'username' => $request->username,
                'email'    => $request->email,
                'role_id' => 2,
                'id_group' => 1,
                'password' => Hash::make($request->password),
            ]);
    
            $token = $user->createToken('auth_token')->plainTextToken;
    
            return response()->json([
                'message' => 'Register berhasil',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user
            ], 201);
    
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat registrasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }



public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    $user = Users::where('email', $credentials['email'])->first();

    if (! $user || ! Hash::check($credentials['password'], $user->password)) {
        return response()->json([
            'message' => 'Email atau password salah'
        ], 401);
    }

    $token = $user->createToken('auth_token')->plainTextToken;


    return response()->json([
        'message' => 'Login berhasil',
        'access_token' => $token,
        'token_type' => 'Bearer',
        'user' => $user,
    ]);

    
}


// logout basic
// public function logout(Request $request)
// {
//     $request->user()->currentAccessToken()->delete();

//     return response()->json([
//         'message' => 'Logout berhasil'
//     ]);
// }

public function logout(Request $request)
{
    $user = $request->user();

    // Hapus semua token user (kalau mau logout dari semua device)
    // $user->tokens()->delete(); 

    // Atau hanya hapus token yang aktif sekarang (lebih umum)
    $user->currentAccessToken()->delete();

    // Pastikan user tidak punya token aktif lagi
    if ($user->tokens()->count() === 0) {
        return response()->json([
            'message' => 'Logout berhasil dan semua token sudah dihapus'
        ]);
    }

    return response()->json([
        'message' => 'Logout berhasil, tetapi masih ada token lain yang aktif'
    ]);
}


}
