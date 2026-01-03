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
    Schema::create('gurus', function (Blueprint $table) {
        $table->id();
        // Relasi ke users (PENTING: ini yang nyambungin ke password/usn di tabel users)
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
        // Data Pribadi Guru
        $table->string('nip')->unique(); // Nomor Induk Pegawai / ID Guru
        $table->string('nama_lengkap');
        $table->string('kelas')->nullable(); // Kelas yang diajar (bisa multiple, pisah koma)
        $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
        $table->string('tempat_lahir');
        $table->date('tanggal_lahir');
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
        Schema::dropIfExists('gurus');
    }
};
