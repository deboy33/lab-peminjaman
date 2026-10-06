<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('labs')) return;

        Schema::create('labs', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('lokasi', 100)->nullable();
            $table->time('jam_buka')->nullable();
            $table->time('jam_tutup')->nullable();
            $table->unsignedTinyInteger('max_durasi_jam')->nullable();
            $table->boolean('boleh_weekend')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('labs');
    }
};
