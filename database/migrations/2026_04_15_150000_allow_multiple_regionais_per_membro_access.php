<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('membro_acesso_regionais', function (Blueprint $table) {
            $table->dropForeign(['membro_id']);
            $table->dropUnique('membro_acesso_regionais_membro_id_unique');
            $table->index('membro_id', 'membro_acesso_regionais_membro_id_index');
            $table->foreign('membro_id')
                ->references('id')
                ->on('membros')
                ->cascadeOnDelete();
            $table->unique(['membro_id', 'regional_id'], 'membro_acesso_regionais_membro_regional_unique');
        });
    }

    public function down(): void
    {
        Schema::table('membro_acesso_regionais', function (Blueprint $table) {
            $table->dropForeign(['membro_id']);
            $table->dropUnique('membro_acesso_regionais_membro_regional_unique');
            $table->dropIndex('membro_acesso_regionais_membro_id_index');
            $table->foreign('membro_id')
                ->references('id')
                ->on('membros')
                ->cascadeOnDelete();
            $table->unique('membro_id');
        });
    }
};
