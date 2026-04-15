<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pre_inscricoes', function (Blueprint $table) {
            $table->foreignId('igreja_id')->nullable()->after('igreja')->constrained('igrejas')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pre_inscricoes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('igreja_id');
        });
    }
};
