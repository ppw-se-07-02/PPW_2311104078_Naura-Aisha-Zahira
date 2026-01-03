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
        Schema::create('presensis', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys
            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
            
            // Data Presensi
            $table->date('tanggal'); // Tanggal presensi
            $table->enum('status', ['hadir', 'izin', 'sakit', 'alpha'])->default('alpha');
            $table->time('waktu')->nullable(); // Waktu absen (jam masuk)
            $table->text('keterangan')->nullable(); // Keterangan tambahan (misal: alasan izin)
            
            // Metadata
            $table->string('dicatat_oleh')->nullable(); // Guru yang mencatat
            
            $table->timestamps();
            
            // Index untuk query cepat
            $table->index(['siswa_id', 'tanggal']);
            $table->index('tanggal');
            $table->index('status');
            
            // Unique constraint: 1 siswa hanya bisa 1 presensi per hari
            $table->unique(['siswa_id', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensis');
    }
};