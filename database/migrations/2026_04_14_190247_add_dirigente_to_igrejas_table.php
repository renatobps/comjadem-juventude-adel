<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('igrejas', function (Blueprint $table) {
            $table->string('dirigente')->default('')->after('bairro');
        });
    }

    public function down(): void
    {
        Schema::table('igrejas', function (Blueprint $table) {
            $table->dropColumn('dirigente');
        });
    }
};
