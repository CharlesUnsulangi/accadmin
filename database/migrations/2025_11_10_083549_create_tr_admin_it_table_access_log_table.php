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
        Schema::create('tr_admin_it_table_access_log', function (Blueprint $table) {
            $table->id();
            $table->string('table_name', 255);
            $table->string('access_type', 50)->default('view')->comment('view, query, export, etc');
            $table->string('frontend_type', 100)->comment('web, mobile-app, api, etc');
            $table->string('user_agent', 500)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name', 100)->nullable();
            $table->text('additional_info')->nullable()->comment('JSON data for extra info');
            $table->timestamp('accessed_at')->useCurrent();
            
            // Indexes for performance
            $table->index('table_name');
            $table->index('access_type');
            $table->index('frontend_type');
            $table->index('user_id');
            $table->index('accessed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_admin_it_table_access_log');
    }
};
