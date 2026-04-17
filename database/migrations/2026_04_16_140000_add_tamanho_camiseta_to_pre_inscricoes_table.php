<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pre_inscricoes', function (Blueprint $table) {
            $table->string('tamanho_camiseta', 4)->nullable()->after('whatsapp');
        });
    }

    public function down(): void
    {
        Schema::table('pre_inscricoes', function (Blueprint $table) {
            $table->dropColumn('tamanho_camiseta');
        });
    }
};
