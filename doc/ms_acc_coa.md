# Tabel: ms_acc_coa

## Deskripsi
Tabel detail akun COA (Level 4). Tabel ini adalah inti dari sistem accounting, berisi akun yang digunakan untuk transaksi dan laporan.

## Struktur Kolom
| Kolom              | Tipe         | Keterangan                        |
|--------------------|--------------|------------------------------------|
| rec_usercreated    | varchar(50)  | User pembuat                      |
| rec_userupdate     | varchar(50)  | User update terakhir               |
| rec_datecreated    | datetime     | Tanggal dibuat                     |
| rec_dateupdate     | datetime     | Tanggal update terakhir            |
| rec_status         | char(1)      | Status (A=Active, D=Deleted, I=Inactive) |
| coa_code           | varchar(50)  | **Primary Key** - Kode akun detail |
| coa_id             | varchar(50)  | ID alternatif                      |
| coa_coasub2code    | varchar(50)  | FK ke ms_acc_coasub2               |
| coa_desc           | varchar(50)  | Deskripsi akun                     |
| coa_note           | varchar(50)  | Catatan tambahan                   |
| arus_kas_code      | varchar(50)  | Kode arus kas                      |
| ms_acc_coa_h       | varchar(50)  | Header COA                         |
| id                 | int (IDENTITY)| Auto-increment ID                 |
| ms_coa_h1_id       | varchar(50)  | Hierarchy H1 ID                    |
| ms_coa_h2_id       | varchar(50)  | Hierarchy H2 ID                    |
| ms_coa_h3_id       | varchar(50)  | Hierarchy H3 ID                    |
| ms_coa_h4_id       | varchar(50)  | Hierarchy H4 ID                    |
| ms_coa_h5_id       | varchar(50)  | Hierarchy H5 ID                    |
| ms_coa_h6_id       | varchar(50)  | Hierarchy H6 ID                    |
| desc_h1            | varchar(50)  | Deskripsi H1                       |
| desc_h2            | varchar(50)  | Deskripsi H2                       |
| desc_h3            | varchar(50)  | Deskripsi H3                       |
| desc_h4            | varchar(50)  | Deskripsi H4                       |
| desc_h5            | varchar(50)  | Deskripsi H5                       |
| desc_h6            | varchar(50)  | Deskripsi H6                       |
| id_h1              | int          | ID H1                              |
| id_h2              | int          | ID H2                              |
| id_h3              | int          | ID H3                              |
| id_h4              | int          | ID H4                              |
| id_h5              | int          | ID H5                              |
| id_h6              | int          | ID H6                              |
| id_old_sub_2       | varchar(50)  | Legacy sub2 ID                     |
| id_old_sub1        | varchar(50)  | Legacy sub1 ID                     |
| id_old_main        | varchar(50)  | Legacy main ID                     |
| sub2_desc          | varchar(50)  | Legacy sub2 description            |
| sub1_desc          | varchar(50)  | Legacy sub1 description            |
| main_desc          | varchar(50)  | Legacy main description            |

## Primary Key
- coa_code

## Foreign Key
- coa_coasub2code → ms_acc_coasub2.coasub2_code

## Extended Properties
- Deskripsi FK: "fk dari coa_coasub_2"
- Deskripsi tabel: "coa detail - level 4"

## Relasi
- **Parent:** ms_acc_coasub2 (Level 3)
- **Child:** (tidak ada, ini level paling detail)

## Hierarchy Support
- Mendukung flexible hierarchy H1-H6 (modern reporting)
- Ada field legacy untuk kompatibilitas sistem lama
