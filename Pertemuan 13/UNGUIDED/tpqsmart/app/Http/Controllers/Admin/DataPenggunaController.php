<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Guru; // ✅ TAMBAHKAN INI
use Illuminate\Http\Request;

class DataPenggunaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // ✅ Ambil data dari database dengan nama variabel yang KONSISTEN
        $guruList = Guru::with('user')->get(); // ✅ GANTI INI
        $siswaList = Siswa::with('user')->get();
        
        // ✅ Debug - Hapus setelah berhasil
        \Log::info('Data Pengguna:', [
            'total_guru' => $guruList->count(),
            'total_siswa' => $siswaList->count()
        ]);

        // ✅ Kalau dari API, return JSON
        if ($request->expectsJson() || $request->is('api/*')) {
        return response()->json([
            'success' => true,
            'data' =>  [
                'guru' => $guruList,
                'siswa' => $siswaList
            ]
        ]);
        }
        
        // ✅ PENTING: Nama variabel harus sama dengan di blade!
        return view('admin.data_pengguna', compact('guruList', 'siswaList'));
        
    }

    /**
     * Show guru detail
     */
    public function showGuru($id)
    {
        // Cari guru berdasarkan ID
        $guru = User::where('role', 'guru')->findOrFail($id);
        
        // Pastikan nama di compact adalah 'guru'
        return view('admin.guru.detail_guru', compact('guru'));
    }   

    /**
     * Show siswa detail
     */
    public function showSiswa($id)
    {
        $siswa = Siswa::with('user')->findOrFail($id);
        return view('admin.siswa.detail', compact('siswa'));
    }

    /**
     * Show the form for creating a new guru.
     */
    public function createGuru()
    {
        return view('admin.guru.create');
    }

    /**
     * Show the form for creating a new siswa.
     */
    public function createSiswa()
    {
        return view('admin.siswa.create');
    }

    /**
     * Store a newly created guru in storage.
     */
    public function storeGuru(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'id_guru' => 'nullable|string|max:50',
            'kelas' => 'nullable|string|max:50',
            'jenis_kelamin' => 'required|in:L,P',
            'ttl' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('photos/guru', 'public');
        }

        $validated['password'] = bcrypt($validated['password']);
        $validated['role'] = 'guru';

        User::create($validated);

        return redirect()->route('admin.data_pengguna')
                         ->with('success', 'Data guru berhasil ditambahkan!');
    }

    /**
     * Store a newly created siswa in storage.
     */
    public function storeSiswa(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'id_siswa' => 'nullable|string|max:50',
            'kelas' => 'nullable|string|max:50',
            'jenis_kelamin' => 'required|in:L,P',
            'ttl' => 'nullable|string|max:255',
            'umur' => 'nullable|integer',
            'alamat' => 'nullable|string',
            'nama_ortu' => 'nullable|string|max:255',
            'telepon_ortu' => 'nullable|string|max:20',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('photos/siswa', 'public');
        }

        $validated['password'] = bcrypt($validated['password']);
        $validated['role'] = 'siswa';

        User::create($validated);

        return redirect()->route('admin.data_pengguna')
                         ->with('success', 'Data siswa berhasil ditambahkan!');
    }

    /**
     * Show the form for editing guru.
     */
    public function editGuru($id)
    {
        $guru = User::where('role', 'guru')->findOrFail($id);
        return view('admin.guru.edit', compact('guru'));
    }

    /**
     * Show the form for editing siswa.
     */
    public function editSiswa($id)
    {
        $siswa = Siswa::with('user')->findOrFail($id);
        return view('admin.siswa.edit', compact('siswa'));
    }

    /**
     * Update guru in storage.
     */
    public function updateGuru(Request $request, $id)
    {
        $guru = User::where('role', 'guru')->findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'id_guru' => 'nullable|string|max:50',
            'kelas' => 'nullable|string|max:50',
            'jenis_kelamin' => 'required|in:L,P',
            'ttl' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'username' => 'required|string|max:255|unique:users,username,' . $id,
            'password' => 'nullable|string|min:8',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            if ($guru->foto) {
                \Storage::disk('public')->delete($guru->foto);
            }
            $validated['foto'] = $request->file('foto')->store('photos/guru', 'public');
        }

        if ($request->filled('password')) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $guru->update($validated);

        return redirect()->route('admin.guru.show', $id)
                         ->with('success', 'Data guru berhasil diperbarui!');
    }

    /**
     * Update siswa in storage.
     */
    public function updateSiswa(Request $request, $id)
    {
        $siswa = Siswa::with('user')->findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'id_siswa' => 'nullable|string|max:50',
            'kelas' => 'nullable|string|max:50',
            'jenis_kelamin' => 'required|in:L,P',
            'ttl' => 'nullable|string|max:255',
            'umur' => 'nullable|integer',
            'alamat' => 'nullable|string',
            'nama_ortu' => 'nullable|string|max:255',
            'telepon_ortu' => 'nullable|string|max:20',
            'username' => 'required|string|max:255|unique:users,username,' . $siswa->user_id,
            'password' => 'nullable|string|min:8',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            if ($siswa->user->foto) {
                \Storage::disk('public')->delete($siswa->user->foto);
            }
            $validated['foto'] = $request->file('foto')->store('photos/siswa', 'public');
        }

        if ($request->filled('password')) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $siswa->user->update($validated);

        return redirect()->route('admin.siswa.show', $id)
                         ->with('success', 'Data siswa berhasil diperbarui!');
    }
    
}