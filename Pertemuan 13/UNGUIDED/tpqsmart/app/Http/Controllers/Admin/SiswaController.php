<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SiswaController extends Controller
{
    /**
     * ============================
     * PRODUKSI: LIST DATA SISWA
     * ============================
     */
    public function index()
    {
        $siswaList = Siswa::with('user')->get(); 
        return view('admin.data_pengguna', compact('siswaList'));
    }

    /**
     * ============================
     * PRODUKSI: DETAIL SISWA
     * ============================
     */
    public function show($id)
    {
        $siswa = Siswa::with('user')->findOrFail($id);

        Log::info('Detail Siswa:', [
            'id' => $siswa->id,
            'nama' => $siswa->nama_lengkap,
            'user_id' => $siswa->user_id
        ]);
    
        return view('admin.data_pengguna.siswa.detail_siswa', compact('siswa'));
    }

    /**
     * ============================
     * PRODUKSI: INSERT SISWA
     * (ELOQUENT ORM)
     * ============================
     */
    public function store(Request $request)
    {
        Log::info('Siswa Store Request:', $request->all());

        $validated = $request->validate([
            'username' => 'required|unique:users,username|min:5',
            'password' => 'required|min:8',
            'idSiswa' => 'required|unique:siswas,nis',
            'nama' => 'required|string|max:255',
            'kelas' => 'required|string',
            'jenisKelamin' => 'required|in:Laki-laki,Perempuan',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string',
            'no_hp' => 'required|string|min:10',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $foto = $request->file('foto');
                $fotoName = time() . '_' . $validated['idSiswa'] . '.' . $foto->getClientOriginalExtension();
                $fotoPath = $foto->storeAs('photos/siswa', $fotoName, 'public');
            }

            $user = User::create([
                'name'     => $validated['nama'],
                'username' => $validated['username'],
                'password' => Hash::make($validated['password']),
                'role'     => 'siswa',
            ]);

            Siswa::create([
                'user_id'       => $user->id,
                'nis'           => $validated['idSiswa'],
                'nama_lengkap'  => $validated['nama'],
                'kelas'         => $validated['kelas'],
                'jenis_kelamin' => $validated['jenisKelamin'],
                'tempat_lahir'  => $validated['tempat_lahir'],
                'tanggal_lahir' => $validated['tanggal_lahir'],
                'alamat'        => $validated['alamat'],
                'no_hp'         => $validated['no_hp'],
                'foto'          => $fotoPath,
            ]);

            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => 'Data siswa berhasil ditambahkan!'
            ], 200);

        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Error creating siswa:', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false, 
                'message' => 'Terjadi kesalahan'
            ], 500);
        }
    }

    /**
     * =================================================
     * TUGAS TEORI 1: INSERT DATA (RAW SQL QUERY)
     * =================================================
     */
    public function storeRawSQL()
    {
        DB::insert(
            "INSERT INTO siswas 
            (nis, nama_lengkap, kelas, jenis_kelamin, tempat_lahir, tanggal_lahir, alamat, no_hp, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())",
            [
                'RAW001',
                'Siswa Raw SQL',
                '1A',
                'Laki-laki',
                'Medan',
                '2015-01-01',
                'Jl. Raw SQL',
                '081234567890'
            ]
        );

        return response()->json([
            'message' => 'Insert menggunakan Raw SQL berhasil'
        ]);
    }

    /**
     * =================================================
     * TUGAS TEORI 2: INSERT DATA (QUERY BUILDER)
     * =================================================
     */
    public function storeQueryBuilder()
    {
        DB::table('siswas')->insert([
            'nis' => 'QB001',
            'nama_lengkap' => 'Siswa Query Builder',
            'kelas' => '1B',
            'jenis_kelamin' => 'Perempuan',
            'tempat_lahir' => 'Binjai',
            'tanggal_lahir' => '2016-02-02',
            'alamat' => 'Jl. Query Builder',
            'no_hp' => '089876543210',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'message' => 'Insert menggunakan Query Builder berhasil'
        ]);
    }

    /**
     * ============================
     * PRODUKSI: EDIT SISWA
     * ============================
     */
    public function edit($id)
    {
        $siswa = Siswa::with('user')->findOrFail($id);
        return view('admin.data_pengguna.siswa.edit_siswa', compact('siswa'));
    }

    /**
     * ============================
     * PRODUKSI: UPDATE SISWA
     * ============================
     */
    public function update(Request $request, $id)
    {
        $siswa = Siswa::with('user')->findOrFail($id);

        $validated = $request->validate([
            'username' => 'required|min:5|unique:users,username,' . $siswa->user_id,
            'password' => 'nullable|min:8',
            'idSiswa' => 'required|unique:siswas,nis,' . $id,
            'nama' => 'required|string|max:255',
            'kelas' => 'required|string',
            'jenisKelamin' => 'required|in:Laki-laki,Perempuan',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string',
            'no_hp' => 'required|string|min:10',
            'foto' => 'nullable|image|max:2048',
        ]);

        DB::beginTransaction();

        try {
            $siswa->update([
                'nis' => $validated['idSiswa'],
                'nama_lengkap' => $validated['nama'],
                'kelas' => $validated['kelas'],
                'jenis_kelamin' => $validated['jenisKelamin'],
                'tempat_lahir' => $validated['tempat_lahir'],
                'tanggal_lahir' => $validated['tanggal_lahir'],
                'alamat' => $validated['alamat'],
                'no_hp' => $validated['no_hp'],
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data siswa berhasil diperbarui!'
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Gagal update data'
            ], 500);
        }
    }

    /**
     * ============================
     * PRODUKSI: DELETE SISWA
     * ============================
     */
    public function destroy($id)
    {
        $siswa = Siswa::with('user')->findOrFail($id);
        $siswa->user->delete();

        return redirect()->back()->with('success', 'Data siswa berhasil dihapus!');
    }
}
