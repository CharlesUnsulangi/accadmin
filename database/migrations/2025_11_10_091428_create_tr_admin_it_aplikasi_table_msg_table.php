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
        Schema::create('tr_admin_it_aplikasi_table_msg', function (Blueprint $table) {
            $table->integer('tr_admin_it_aplikasi_table_msg_id')->primary();
            $table->string('tr_aplikasi_table_id', 50)->nullable();
            $table->text('msg_desc')->nullable();
            $table->string('user_created', 50)->nullable();
            $table->date('date_created')->nullable();
            
            // Index untuk performa
            $table->index('tr_aplikasi_table_id');
            $table->index('date_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_admin_it_aplikasi_table_msg');
    }
};
