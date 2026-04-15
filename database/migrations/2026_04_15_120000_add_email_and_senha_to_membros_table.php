<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('membros', function (Blueprint $table) {
            $table->string('email')->nullable()->unique()->after('nome');
            $table->string('senha')->nullable()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('membros', function (Blueprint $table) {
            $table->dropUnique('membros_email_unique');
            $table->dropColumn(['email', 'senha']);
        });
    }
};
