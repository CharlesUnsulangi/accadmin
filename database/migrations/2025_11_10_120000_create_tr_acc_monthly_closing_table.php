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
        Schema::create('tr_acc_monthly_closing', function (Blueprint $table) {
            // Primary Key
            $table->id();
            
            // Version Control
            $table->integer('version_number')->default(1)->comment('Version 1, 2, 3, dst');
            $table->string('version_status', 20)->default('DRAFT')->comment('DRAFT, ACTIVE, SUPERSEDED, ARCHIVED');
            $table->string('version_note', 500)->nullable()->comment('Reason for new version');
            
            // Periode
            $table->integer('closing_year')->comment('Tahun closing (2024, 2025)');
            $table->integer('closing_month')->comment('Bulan closing (1-12)');
            $table->string('closing_periode_id', 6)->comment('YYYYMM (202401)');
            
            // COA Hierarchy (Denormalized untuk reporting - 4 tingkat)
            // Level 1: Main Category (ms_acc_coa_main)
            $table->string('coa_main_code', 10)->nullable()->comment('Main category code');
            $table->string('coa_main_desc', 100)->nullable()->comment('Main category description');
            
            // Level 2: Sub Category 1 (ms_acc_coasub1)
            $table->string('coasub1_code', 50)->nullable()->comment('Sub category 1 code');
            $table->string('coasub1_desc', 100)->nullable()->comment('Sub category 1 description');
            
            // Level 3: Sub Category 2 (ms_acc_coasub2)
            $table->string('coasub2_code', 50)->nullable()->comment('Sub category 2 code');
            $table->string('coasub2_desc', 100)->nullable()->comment('Sub category 2 description');
            
            // Level 4: Detail Account (ms_acc_coa)
            $table->string('coa_code', 50)->comment('Kode akun COA');
            $table->string('coa_desc')->nullable()->comment('Deskripsi akun COA');
            
            // Opening Balance (dari bulan/tahun sebelumnya)
            $table->decimal('opening_debet', 18, 2)->default(0)->comment('Saldo awal debet');
            $table->decimal('opening_kredit', 18, 2)->default(0)->comment('Saldo awal kredit');
            $table->decimal('opening_balance', 18, 2)->default(0)->comment('Saldo awal netto');
            
            // Mutasi Bulan Ini (dari tr_acc_transaksi_coa)
            $table->decimal('mutasi_debet', 18, 2)->default(0)->comment('Total debet bulan ini');
            $table->decimal('mutasi_kredit', 18, 2)->default(0)->comment('Total kredit bulan ini');
            $table->decimal('mutasi_netto', 18, 2)->default(0)->comment('Netto mutasi (debet - kredit)');
            $table->integer('jumlah_transaksi')->default(0)->comment('Jumlah transaksi dalam periode');
            
            // Closing Balance
            $table->decimal('closing_debet', 18, 2)->default(0)->comment('Saldo akhir debet');
            $table->decimal('closing_kredit', 18, 2)->default(0)->comment('Saldo akhir kredit');
            $table->decimal('closing_balance', 18, 2)->default(0)->comment('Saldo akhir netto');
            
            // Status & Lock
            $table->boolean('is_closed')->default(false)->comment('True = locked, cannot modify');
            $table->dateTime('closed_at')->nullable()->comment('Waktu closing dilakukan');
            $table->string('closed_by', 50)->nullable()->comment('User yang melakukan closing');
            
            // Superseded Info (jika ada version baru)
            $table->dateTime('superseded_at')->nullable()->comment('Waktu version ini diganti');
            $table->string('superseded_by', 50)->nullable()->comment('User yang membuat version baru');
            $table->integer('superseded_by_version')->nullable()->comment('Version yang menggantikan');
            
            // Audit Trail
            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();
            
            // Note: Indexes dan FK akan dibuat setelah alter column coa_code
        });
        
        // Alter coa_code to VARCHAR to match ms_acc_coa (must do before creating indexes)
        DB::statement("ALTER TABLE tr_acc_monthly_closing ALTER COLUMN coa_code VARCHAR(50) NOT NULL");
        
        // Add Indexes after altering column type
        Schema::table('tr_acc_monthly_closing', function (Blueprint $table) {
            $table->index(['closing_periode_id', 'coa_code', 'version_number'], 'idx_periode_coa_version');
            $table->index(['version_status'], 'idx_version_status');
            $table->index(['closing_year', 'closing_month'], 'idx_year_month');
            $table->index(['coa_code'], 'idx_coa_code');
            $table->index(['is_closed'], 'idx_is_closed');
        });
        
        // Add Foreign Key after altering column type
        Schema::table('tr_acc_monthly_closing', function (Blueprint $table) {
            $table->foreign('coa_code')
                  ->references('coa_code')
                  ->on('ms_acc_coa')
                  ->onDelete('no action');
        });
        
        // Add table comment
        DB::statement("EXEC sp_addextendedproperty 
            @name = N'MS_Description', 
            @value = N'Tabel closing bulanan dengan version control untuk rekap transaksi per COA', 
            @level0type = N'SCHEMA', @level0name = 'dbo',
            @level1type = N'TABLE', @level1name = 'tr_acc_monthly_closing'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_acc_monthly_closing');
    }
};
