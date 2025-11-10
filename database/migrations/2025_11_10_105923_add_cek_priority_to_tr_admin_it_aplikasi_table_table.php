<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tr_admin_it_aplikasi_table', function (Blueprint $table) {
            $table->boolean('cek_priority')->default(false)->after('table_note');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tr_admin_it_aplikasi_table', function (Blueprint $table) {
            $table->dropColumn('cek_priority');
        });
    }
};
