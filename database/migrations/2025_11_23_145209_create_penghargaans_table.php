<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penghargaans', function (Blueprint $table) {
        $table->id();
        $table->string('nama');
        $table->date('tanggal')->nullable();
        $table->string('status')->default('Menunggu');
        $table->timestamps();
    });

    }

    public function down(): void
    {
        Schema::dropIfExists('penghargaans');
    }
};
