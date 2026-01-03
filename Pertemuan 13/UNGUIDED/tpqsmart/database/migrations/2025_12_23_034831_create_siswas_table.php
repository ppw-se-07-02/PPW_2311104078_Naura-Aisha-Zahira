<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('siswas', function (Blueprint $table) {
        $table->id();
        // Relasi ke tabel users (Menghubungkan profil dengan akun login)
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->string('nis')->unique(); // ID Siswa
        $table->string('nama_lengkap');
        $table->string('kelas');
        $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
        $table->string('tempat_lahir');
        $table->date('tanggal_lahir'); // Pake tipe date buat kalender
        $table->text('alamat');
        $table->string('no_hp');
        $table->string('foto')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
