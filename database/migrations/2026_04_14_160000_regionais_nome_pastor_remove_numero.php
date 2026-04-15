<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('regionais', function (Blueprint $table) {
            $table->string('pastor_responsavel')->default('')->after('nome');
        });

        DB::table('regionais')->where('pastor_responsavel', '')->update(['pastor_responsavel' => '']);

        Schema::table('regionais', function (Blueprint $table) {
            $table->dropUnique(['numero']);
            $table->dropColumn('numero');
        });
    }

    public function down(): void
    {
        Schema::table('regionais', function (Blueprint $table) {
            $table->unsignedTinyInteger('numero')->nullable()->after('id');
            $table->dropColumn('pastor_responsavel');
        });

        Schema::table('regionais', function (Blueprint $table) {
            $table->unique('numero');
        });
    }
};
