<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lokasi', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->text('alamat');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('link_maps', 500)->nullable();
            $table->text('lingkungan_sekitar')->nullable();
            $table->timestamps();
        });

        Schema::create('unit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lokasi_id')->constrained('lokasi')->restrictOnDelete();
            $table->string('nomor_unit', 30)->unique();
            $table->unsignedBigInteger('tarif_bulanan');
            $table->unsignedBigInteger('tarif_tahunan')->nullable();
            $table->unsignedTinyInteger('jumlah_kamar')->default(1);
            $table->decimal('luas_bangunan', 8, 2)->nullable();
            $table->text('fasilitas')->nullable();
            $table->string('status', 20)->default('kosong')->index();
            $table->string('ketentuan_penghuni')->nullable();
            $table->boolean('izin_hewan')->default(false);
            $table->timestamps();
        });

        Schema::create('penyewa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->string('nama');
            $table->string('nik', 16)->nullable()->unique();
            $table->string('no_whatsapp', 20);
            $table->text('alamat_asal')->nullable();
            $table->string('foto_ktp')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::create('kontrak_sewa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained('unit')->restrictOnDelete();
            $table->foreignId('penyewa_id')->constrained('penyewa')->restrictOnDelete();
            $table->string('periode', 20);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->unsignedBigInteger('tarif');
            $table->string('status', 20)->default('diajukan')->index();
            $table->string('file_perjanjian')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kontrak_sewa_id')->constrained('kontrak_sewa')->cascadeOnDelete();
            $table->date('jatuh_tempo')->index();
            $table->unsignedBigInteger('nominal');
            $table->date('tanggal_bayar')->nullable();
            $table->string('metode', 20)->nullable();
            $table->string('bukti_bayar')->nullable();
            // Status sengaja biner sesuai batasan sistem: lunas / nunggak.
            // "Menunggu verifikasi" = nunggak + bukti_bayar sudah diunggah.
            $table->string('status', 20)->default('nunggak')->index();
            $table->timestamp('diverifikasi_pada')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['kontrak_sewa_id', 'jatuh_tempo']);
        });

        Schema::create('perbaikan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained('unit')->restrictOnDelete();
            $table->foreignId('penyewa_id')->nullable()->constrained('penyewa')->nullOnDelete();
            $table->string('jenis', 20)->default('kerusakan');
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->string('foto')->nullable();
            $table->date('tanggal_lapor');
            $table->date('tanggal_selesai')->nullable();
            $table->string('status', 20)->default('dilaporkan')->index();
            $table->timestamps();
        });

        Schema::create('pengeluaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->nullable()->constrained('unit')->nullOnDelete();
            $table->foreignId('perbaikan_id')->nullable()->constrained('perbaikan')->cascadeOnDelete();
            $table->string('kategori', 20);
            $table->date('tanggal')->index();
            $table->string('keterangan');
            $table->unsignedBigInteger('nominal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengeluaran');
        Schema::dropIfExists('perbaikan');
        Schema::dropIfExists('pembayaran');
        Schema::dropIfExists('kontrak_sewa');
        Schema::dropIfExists('penyewa');
        Schema::dropIfExists('unit');
        Schema::dropIfExists('lokasi');
    }
};
