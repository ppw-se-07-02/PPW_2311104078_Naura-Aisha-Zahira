<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mahasiswas', function (Blueprint $table) {
            $table->id(); 
            $table->char('nim',8)->unique();
            $table->string('nama'); 
            $table->string('tempat_lahir'); 
            $table->date('tanggal_lahir'); 
            $table->string('fakultas'); 
            $table->string('jurusan'); 
            $table->decimal('ipk',3,2)->default(1.00);
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('mahasiswas');
    }
};
