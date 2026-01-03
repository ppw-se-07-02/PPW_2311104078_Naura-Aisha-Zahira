<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GuruController extends Controller
{
    /**
     * Display a listing of guru
     */
    public function index()
    {
        $guruList = Guru::with('user')->get();
        return view('admin.data_pengguna', compact('guruList'));
    }

    /**
     * Show the form for creating a new guru
     */
    public function create()
    {
        return view('admin.data_pengguna.guru.tambah_guru');
    }

    /**
     * Store a newly created guru
     */
    public function store(Request $request)
    {
        Log::info('Guru Store Request:', $request->all());

        // Validasi Input
        $validated = $request->validate([
            'username' => 'required|unique:users,username|min:5',
            'password' => 'required|min:8',
            'nip' => 'required|unique:gurus,nip',
            'nama' => 'required|string|max:255',
            'kelas' => 'nullable|string',
            'jenisKelamin' => 'required|in:Laki-laki,Perempuan',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string',
            'no_hp' => 'required|string|min:10',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'username.required' => 'Username wajib diisi',
            'username.unique' => 'Username sudah digunakan',
            'username.min' => 'Username minimal 5 karakter',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'nip.required' => 'NIP wajib diisi',
            'nip.unique' => 'NIP sudah digunakan',
            'nama.required' => 'Nama lengkap wajib diisi',
            'jenisKelamin.required' => 'Jenis kelamin wajib dipilih',
            'tempat_lahir.required' => 'Tempat lahir wajib diisi',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi',
            'alamat.required' => 'Alamat wajib diisi',
            'no_hp.required' => 'Nomor handphone wajib diisi',
            'no_hp.min' => 'Nomor handphone minimal 10 digit',
            'foto.image' => 'File harus berupa gambar',
            'foto.max' => 'Ukuran foto maksimal 2MB',
        ]);

        try {
            DB::beginTransaction();

            // Handle foto upload
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $foto = $request->file('foto');
                $fotoName = time() . '_' . $validated['nip'] . '.' . $foto->getClientOriginalExtension();
                $fotoPath = $foto->storeAs('photos/guru', $fotoName, 'public');
            }

            // Simpan ke tabel Users (untuk login)
            $user = User::create([
                'name'     => $validated['nama'],
                'username' => $validated['username'],
                'password' => Hash::make($validated['password']),
                'role'     => 'guru',
            ]);

            // Simpan ke tabel Gurus (biodata)
            Guru::create([
                'user_id'       => $user->id,
                'nip'           => $validated['nip'],
                'nama_lengkap'  => $validated['nama'],
                'kelas'         => $validated['kelas'] ?? null,
                'jenis_kelamin' => $validated['jenisKelamin'],
                'tempat_lahir'  => $validated['tempat_lahir'],
                'tanggal_lahir' => $validated['tanggal_lahir'],
                'alamat'        => $validated['alamat'],
                'no_hp'         => $validated['no_hp'],
                'foto'          => $fotoPath,
                'status'        => 'Aktif',
            ]);

            DB::commit();

            Log::info('Guru created successfully:', ['user_id' => $user->id]);

            return response()->json([
                'success' => true,
                'message' => 'Data guru berhasil ditambahkan!'
            ], 200);

        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Error creating guru:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified guru
     */
    public function show($id)
    {
        try {
            $guru = Guru::with('user')->findOrFail($id);

            Log::info('Showing guru detail:', [
                'guru_id' => $guru->id,
                'nama' => $guru->nama_lengkap,
                'nip' => $guru->nip,
            ]);

            return view('admin.data_pengguna.guru.detail_guru', compact('guru'));

        } catch (\Exception $e) {
            Log::error('Error showing guru:', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('admin.data_pengguna')
                           ->with('error', 'Data guru tidak ditemukan!');
        }
    }

    /**
     * Show the form for editing guru
     */
    public function edit($id)
    {
        $guru = Guru::with('user')->findOrFail($id);
        return view('admin.data_pengguna.guru.edit_guru', compact('guru'));
    }

    /**
     * Update the specified guru
     */
    public function update(Request $request, $id)
    {
        Log::info('Guru Update Request:', $request->all());

        $guru = Guru::with('user')->findOrFail($id);

        // Validasi Input
        $validated = $request->validate([
            'username' => 'required|min:5|unique:users,username,' . $guru->user_id,
            'password' => 'nullable|min:8',
            'nip' => 'required|unique:gurus,nip,' . $id,
            'nama' => 'required|string|max:255',
            'kelas' => 'nullable|string',
            'jenisKelamin' => 'required|in:Laki-laki,Perempuan',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string',
            'no_hp' => 'required|string|min:10',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            DB::beginTransaction();

            // Handle foto upload baru
            if ($request->hasFile('foto')) {
                // Hapus foto lama jika ada
                if ($guru->foto && \Storage::disk('public')->exists($guru->foto)) {
                    \Storage::disk('public')->delete($guru->foto);
                }

                $foto = $request->file('foto');
                $fotoName = time() . '_' . $validated['nip'] . '.' . $foto->getClientOriginalExtension();
                $validated['foto'] = $foto->storeAs('photos/guru', $fotoName, 'public');
            }

            // Update User
            $userData = [
                'name' => $validated['nama'],
                'username' => $validated['username'],
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($validated['password']);
            }

            $guru->user->update($userData);

            // Update Guru
            $guru->update([
                'nip'           => $validated['nip'],
                'nama_lengkap'  => $validated['nama'],
                'kelas'         => $validated['kelas'] ?? null,
                'jenis_kelamin' => $validated['jenisKelamin'],
                'tempat_lahir'  => $validated['tempat_lahir'],
                'tanggal_lahir' => $validated['tanggal_lahir'],
                'alamat'        => $validated['alamat'],
                'no_hp'         => $validated['no_hp'],
                'foto'          => $validated['foto'] ?? $guru->foto,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data guru berhasil diperbarui!'
            ], 200);

        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Error updating guru:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified guru
     */
    public function destroy($id)
    {
        try {
            $guru = Guru::with('user')->findOrFail($id);

            // Hapus foto jika ada
            if ($guru->foto && \Storage::disk('public')->exists($guru->foto)) {
                \Storage::disk('public')->delete($guru->foto);
            }

            // Hapus user (cascade akan hapus guru juga)
            $guru->user->delete();

            return redirect()->route('admin.data_pengguna')
                           ->with('success', 'Data guru berhasil dihapus!');

        } catch (\Exception $e) {
            Log::error('Error deleting guru:', [
                'message' => $e->getMessage()
            ]);

            return redirect()->route('admin.data_pengguna')
                           ->with('error', 'Gagal menghapus data guru!');
        }
    }
}