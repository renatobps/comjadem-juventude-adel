<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membros', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->foreignId('cargo_id')->constrained('cargos')->restrictOnDelete();
            $table->string('telefone', 40);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membros');
    }
};
