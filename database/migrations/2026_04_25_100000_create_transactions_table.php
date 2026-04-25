<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->decimal('jumlah', 15, 2)->default(0);
            $table->string('tipe'); // pemasukan / pengeluaran
            $table->date('tanggal');
            $table->string('kategori')->default('Lainnya');
            $table->string('user_id')->nullable(); // email user
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
