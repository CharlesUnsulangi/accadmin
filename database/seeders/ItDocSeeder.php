<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ItDoc;

class ItDocSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $docs = [
            [
                'topik' => 'ms_acc_coa - Chart of Accounts',
                'project' => 'AccAdmin',
                'catatan_text' => 'CREATE TABLE [dbo].[ms_acc_coa](
	[rec_comcode] [varchar](2) NOT NULL,
	[rec_areacode] [varchar](3) NOT NULL,
	[coa_code] [varchar](30) NOT NULL,
	[coa_desc] [varchar](100) NULL,
	[rec_status] [varchar](1) NULL,
	[coa_desc1] [varchar](100) NULL,
	[coa_desc2] [varchar](100) NULL,
	[coa_desc3] [varchar](100) NULL,
	[coa_desc4] [varchar](100) NULL,
	[coa_desc5] [varchar](100) NULL,
	[coa_desc6] [varchar](100) NULL,
	[ms_coa_h1_id] [int] NULL,
	[ms_coa_h2_id] [int] NULL,
	[ms_coa_h3_id] [int] NULL,
	[ms_coa_h4_id] [int] NULL,
	[ms_coa_h5_id] [int] NULL,
	[ms_coa_h6_id] [int] NULL,
	[arus_kas_code] [varchar](30) NULL,
	[arus_kas_desc] [varchar](100) NULL,
	[coa_coasub2code] [varchar](30) NULL,
 CONSTRAINT [PK_ms_acc_coa] PRIMARY KEY CLUSTERED 
(
	[rec_comcode] ASC,
	[rec_areacode] ASC,
	[coa_code] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]

KETERANGAN:
- Tabel COA dengan dual hierarchy support
- Legacy hierarchy: coa_coasub2code (4-level: Main→Sub1→Sub2→COA)
- Modern hierarchy: ms_coa_h1_id sampai ms_coa_h6_id (flexible 6-level hierarchy)
- coa_code adalah Primary Key
- Hubungan: belongsTo CoaMain, CoaSub1, CoaSub2 (legacy), belongsTo CoaH1-H6 (modern)',
                'link' => null,
                'created_date' => now(),
                'created_user' => 'System',
            ],
            [
                'topik' => 'ms_acc_cheque_h - Cheque Book Header',
                'project' => 'AccAdmin',
                'catatan_text' => 'CREATE TABLE [dbo].[ms_acc_cheque_h](
	[rec_comcode] [varchar](2) NOT NULL,
	[rec_areacode] [varchar](3) NOT NULL,
	[cheque_code_h] [varchar](30) NOT NULL,
	[cheque_desc] [varchar](100) NULL,
	[cheque_bank] [varchar](100) NULL,
	[cheque_cabang] [varchar](100) NULL,
	[cheque_rek] [varchar](100) NULL,
	[cheque_coacode] [varchar](30) NULL,
	[cheque_startno] [varchar](30) NULL,
	[cheque_endno] [varchar](30) NULL,
	[rec_status] [varchar](1) NULL,
 CONSTRAINT [PK_ms_acc_cheque_h] PRIMARY KEY CLUSTERED 
(
	[rec_comcode] ASC,
	[rec_areacode] ASC,
	[cheque_code_h] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]

KETERANGAN:
- Master data buku cheque
- cheque_startno sampai cheque_endno menentukan range nomor cheque
- Related dengan COA via cheque_coacode
- hasMany relation ke ms_acc_cheque_d (individual cheques)',
                'link' => null,
                'created_date' => now(),
                'created_user' => 'System',
            ],
            [
                'topik' => 'ms_acc_cheque_d - Individual Cheque Details',
                'project' => 'AccAdmin',
                'catatan_text' => 'CREATE TABLE [dbo].[ms_acc_cheque_d](
	[rec_comcode] [varchar](2) NOT NULL,
	[rec_areacode] [varchar](3) NOT NULL,
	[cheque_code_h] [varchar](30) NOT NULL,
	[cheque_code_d] [varchar](30) NOT NULL,
	[cheque_date] [date] NULL,
	[cheque_value] [money] NULL,
	[cheque_status] [varchar](100) NULL,
	[cheque_purpose] [varchar](300) NULL,
 CONSTRAINT [PK_ms_acc_cheque_d] PRIMARY KEY CLUSTERED 
(
	[rec_comcode] ASC,
	[rec_areacode] ASC,
	[cheque_code_h] ASC,
	[cheque_code_d] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]

KETERANGAN:
- Detail cheque individual (inventory)
- Composite PK: rec_comcode + rec_areacode + cheque_code_h + cheque_code_d
- cheque_status: AVAILABLE, USED, VOID
- belongsTo relation ke ms_acc_cheque_h
- Scopes: available(), used(), void()',
                'link' => null,
                'created_date' => now(),
                'created_user' => 'System',
            ],
            [
                'topik' => 'tr_acc_transaksi_cheque - Cheque Transaction Header',
                'project' => 'AccAdmin',
                'catatan_text' => 'CREATE TABLE [dbo].[tr_acc_transaksi_cheque](
	[rec_comcode] [varchar](2) NOT NULL,
	[rec_areacode] [varchar](3) NOT NULL,
	[transcheque_code] [varchar](30) NOT NULL,
	[transcheque_transmaincode] [varchar](30) NULL,
	[transcheque_vendor] [varchar](300) NULL,
	[transcheque_value] [money] NULL,
	[transcheque_date] [date] NULL,
	[transcheque_status] [varchar](30) NULL,
	[transcheque_doc] [varchar](100) NULL,
 CONSTRAINT [PK_tr_acc_transaksi_cheque] PRIMARY KEY CLUSTERED 
(
	[rec_comcode] ASC,
	[rec_areacode] ASC,
	[transcheque_code] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]

KETERANGAN:
- Header transaksi pembayaran dengan cheque (voucher pembayaran)
- transcheque_transmaincode: link ke tr_acc_transaksi_main (journal entry)
- transcheque_vendor: penerima pembayaran
- hasMany relation ke tr_acc_transaksi_cheque_d (detail cheque yang digunakan)
- Flow: Voucher Pembayaran → Menggunakan Cheque → Create Journal Entry',
                'link' => null,
                'created_date' => now(),
                'created_user' => 'System',
            ],
            [
                'topik' => 'tr_acc_transaksi_cheque_d - Cheque Transaction Details',
                'project' => 'AccAdmin',
                'catatan_text' => 'CREATE TABLE [dbo].[tr_acc_transaksi_cheque_d](
	[rec_comcode] [varchar](2) NOT NULL,
	[rec_areacode] [varchar](3) NOT NULL,
	[transcheque_code_h] [varchar](30) NOT NULL,
	[transcheque_no] [varchar](30) NOT NULL,
	[transcheque_value] [money] NULL,
	[transcheque_datedoc] [date] NULL,
	[transcheque_coa] [varchar](30) NULL,
 CONSTRAINT [PK_tr_acc_transaksi_cheque_d] PRIMARY KEY CLUSTERED 
(
	[rec_comcode] ASC,
	[rec_areacode] ASC,
	[transcheque_code_h] ASC,
	[transcheque_no] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]

KETERANGAN:
- Detail cheque yang digunakan dalam transaksi pembayaran
- transcheque_no: nomor cheque dari ms_acc_cheque_d
- transcheque_coa: COA untuk pencatatan
- Composite PK: rec_comcode + rec_areacode + transcheque_code_h + transcheque_no
- belongsTo relation ke tr_acc_transaksi_cheque (header)
- Saat cheque digunakan, status di ms_acc_cheque_d update menjadi USED',
                'link' => null,
                'created_date' => now(),
                'created_user' => 'System',
            ],
            [
                'topik' => 'tr_admin_it_doc - IT Documentation Storage',
                'project' => 'AccAdmin',
                'catatan_text' => 'CREATE TABLE [dbo].[tr_admin_it_doc](
	[tr_admin_it_doc_id] [int] IDENTITY(1,1) NOT NULL,
	[catatan_text] [text] NULL,
	[created_date] [date] NULL,
	[created_user] [varchar](300) NULL,
	[topik] [varchar](300) NULL,
	[project] [varchar](300) NULL,
	[link] [varchar](300) NULL,
 CONSTRAINT [PK_tr_admin_it_doc] PRIMARY KEY CLUSTERED 
(
	[tr_admin_it_doc_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]

KETERANGAN:
- Tabel untuk menyimpan dokumentasi IT
- tr_admin_it_doc_id: IDENTITY primary key
- catatan_text: TEXT field untuk dokumentasi lengkap (schema, notes, etc)
- topik: kategori dokumentasi (Database Schema, API, etc)
- project: nama project (AccAdmin, HRD System, etc)
- link: referensi URL jika ada
- Digunakan untuk menyimpan semua database schema, change logs, developer notes',
                'link' => null,
                'created_date' => now(),
                'created_user' => 'System',
            ],
        ];

        foreach ($docs as $doc) {
            ItDoc::create($doc);
        }
    }
}
