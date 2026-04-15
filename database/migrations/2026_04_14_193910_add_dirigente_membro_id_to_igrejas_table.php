<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('igrejas', function (Blueprint $table) {
            $table->foreignId('dirigente_membro_id')
                ->nullable()
                ->after('dirigente')
                ->constrained('membros')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('igrejas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('dirigente_membro_id');
        });
    }
};
