<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasis', function (Blueprint $table) {
            $table->id();
            
            // Penerima
            $table->string('no_hp'); // Nomor WhatsApp tujuan
            $table->string('nama_penerima'); // Nama penerima
            $table->enum('tipe_penerima', ['orang_tua', 'guru', 'kelas', 'semua'])->default('orang_tua');
            
            // Relasi (optional - jika ada siswa_id atau guru_id terkait)
            $table->foreignId('siswa_id')->nullable()->constrained('siswas')->onDelete('cascade');
            $table->foreignId('guru_id')->nullable()->constrained('users')->onDelete('cascade');
            
            // Konten Pesan
            $table->enum('tipe_notifikasi', ['presensi', 'perkembangan', 'manual', 'pengumuman'])->default('manual');
            $table->text('pesan'); // Isi pesan WhatsApp
            
            // Status Pengiriman
            $table->enum('status', ['pending', 'berhasil', 'gagal'])->default('pending');
            $table->text('error_message')->nullable(); // Jika gagal, simpan error
            $table->timestamp('sent_at')->nullable(); // Waktu terkirim
            
            // Metadata
            $table->string('reference_type')->nullable(); // Presensi, Perkembangan, dll
            $table->unsignedBigInteger('reference_id')->nullable(); // ID dari presensi/perkembangan
            $table->foreignId('sent_by')->nullable()->constrained('users')->onDelete('set null'); // Admin yang kirim
            
            $table->timestamps();
            
            // Indexes
            $table->index('no_hp');
            $table->index('status');
            $table->index('tipe_notifikasi');
            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasis');
    }
};