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
        // Drop FK karena banyak COA di transaksi tidak ada di master table
        Schema::table('tr_acc_monthly_closing', function (Blueprint $table) {
            $table->dropForeign('tr_acc_monthly_closing_coa_code_foreign');
        });
        
        Schema::table('tr_acc_yearly_closing', function (Blueprint $table) {
            $table->dropForeign('tr_acc_yearly_closing_coa_code_foreign');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-add FK jika rollback
        Schema::table('tr_acc_monthly_closing', function (Blueprint $table) {
            $table->foreign('coa_code')
                  ->references('coa_code')
                  ->on('ms_acc_coa')
                  ->onDelete('no action');
        });
        
        Schema::table('tr_acc_yearly_closing', function (Blueprint $table) {
            $table->foreign('coa_code')
                  ->references('coa_code')
                  ->on('ms_acc_coa')
                  ->onDelete('no action');
        });
    }
};
