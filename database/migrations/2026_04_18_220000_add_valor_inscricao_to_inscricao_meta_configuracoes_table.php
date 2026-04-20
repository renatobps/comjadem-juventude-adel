<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inscricao_meta_configuracoes', function (Blueprint $table) {
            $table->decimal('valor_inscricao', 10, 2)->default(0)->after('meta_total');
        });
    }

    public function down(): void
    {
        Schema::table('inscricao_meta_configuracoes', function (Blueprint $table) {
            $table->dropColumn('valor_inscricao');
        });
    }
};
