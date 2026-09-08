<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pekerjaans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('daftar_pekerjaan');
            $table->integer('total_pekerjaan');
            $table->string('file_name')->nullable();
            $table->date('tanggal');
            $table->timestamps();
        });
    }
};
