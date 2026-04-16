<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('membros', function (Blueprint $table) {
            $table->dropForeign(['cargo_id']);
            $table->foreignId('cargo_id')->nullable()->change();
            $table->foreign('cargo_id')->references('id')->on('cargos')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('membros', function (Blueprint $table) {
            $table->dropForeign(['cargo_id']);
            $table->foreignId('cargo_id')->nullable(false)->change();
            $table->foreign('cargo_id')->references('id')->on('cargos')->restrictOnDelete();
        });
    }
};
