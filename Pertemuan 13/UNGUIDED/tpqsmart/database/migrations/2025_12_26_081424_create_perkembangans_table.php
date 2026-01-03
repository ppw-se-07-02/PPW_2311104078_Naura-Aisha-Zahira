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
        Schema::create('perkembangans', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys
            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
            
            // Data Perkembangan
            $table->date('tanggal'); // Tanggal pencatatan
            
            // Tilawati/Bacaan
            $table->string('tilawati')->nullable(); // Jilid 1-6 / Al-Quran
            $table->string('halaman')->nullable(); // Halaman berapa
            
            // Kemampuan/Penilaian
            $table->enum('kemampuan', ['Sangat Baik', 'Baik', 'Cukup', 'Perlu Bimbingan'])->nullable();
            
            // Hafalan
            $table->string('hafalan')->nullable(); // Surah yang dihafal
            $table->string('ayat')->nullable(); // Ayat berapa
            
            // Perilaku & Catatan
            $table->text('tata_krama')->nullable(); // Catatan perilaku
            $table->text('catatan')->nullable(); // Catatan tambahan guru
            
            // Metadata
            $table->string('dicatat_oleh')->nullable(); // Guru yang mencatat
            
            $table->timestamps();
            
            // Index untuk query cepat
            $table->index(['siswa_id', 'tanggal']);
            $table->index('tanggal');
            
            // Unique constraint: 1 siswa 1 catatan per hari
            $table->unique(['siswa_id', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perkembangans');
    }
};