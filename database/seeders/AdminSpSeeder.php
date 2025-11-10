<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminSpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('ms_admin_sp')->insert([
            [
                'ms_admin_sp_id' => 'SP_Test_Cari_orphan_tr_acc_transaksi_coa_to_tr_acc_transaksi_main',
                'sp_desc' => 'Find orphan transaction COA records',
                'date_start_input' => null,
                'date_end_input' => null,
                'money_input' => null,
                'varchar_input' => null,
                'sp_name' => 'SP_Test_Cari_orphan_coa',
            ],
        ]);
    }
}
