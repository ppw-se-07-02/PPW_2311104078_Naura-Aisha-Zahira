<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Siswa;

class AuthController extends Controller
{
    // ============================================
    // 1. LOGIN WEB (untuk Admin & Guru)
    // ============================================
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            // Redirect based on role
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard_admin');
            } else if ($user->role === 'guru') {
                return redirect()->route('guru.dashboard');
            }
            
            return redirect('/');
        }

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ]);
    }

    // ============================================
    // 2. LOGIN API (untuk Mobile App)
    // ============================================
    public function loginApi(Request $request)
    {
        \Log::info('Login API Request:', $request->all());

        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $username = $request->username;
        $password = $request->password;

        // ===== CEK LOGIN SEBAGAI GURU/ADMIN =====
        $user = User::where('username', $username)->first();
        
        if ($user && Hash::check($password, $user->password)) {
            \Log::info('Login success as User:', ['user_id' => $user->id, 'role' => $user->role]);

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil',
                'data' => [
                    'user_id' => $user->id,
                    'username' => $user->username,
                    'nama' => $user->name,
                    'role' => $user->role,
                    'token' => 'user_' . $user->id // Simple token untuk testing
                ]
            ], 200);
        }

        // ===== CEK LOGIN SEBAGAI ORANG TUA =====
        // Login pakai no_hp (username) dan no_hp (password)
        $siswa = Siswa::where('no_hp', $username)->first();
        
        if ($siswa && $password === $siswa->no_hp) {
            \Log::info('Login success as Ortu:', ['siswa_id' => $siswa->id]);

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil',
                'data' => [
                    'user_id' => $siswa->id,
                    'username' => $siswa->no_hp,
                    'nama' => 'Orang Tua ' . $siswa->nama_lengkap,
                    'role' => 'ortu',
                    'siswa_id' => $siswa->id,
                    'nama_siswa' => $siswa->nama_lengkap,
                    'token' => 'ortu_' . $siswa->id
                ]
            ], 200);
        }

        \Log::warning('Login failed:', ['username' => $username]);

        return response()->json([
            'success' => false,
            'message' => 'Username atau password salah'
        ], 401);
    }

    // ============================================
    // 3. LOGOUT
    // ============================================
    public function logout(Request $request)
    {
        Auth::logout();
        return redirect('/login');
    }
}