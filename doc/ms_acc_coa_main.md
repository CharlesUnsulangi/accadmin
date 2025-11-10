# Tabel: ms_acc_coa_main

## Deskripsi
Tabel kategori utama COA (Level 1 - paling atas). Digunakan untuk mengelompokkan akun berdasarkan kategori besar seperti Asset, Liability, Equity, Revenue, Expense.

## Struktur Kolom
| Kolom                | Tipe         | Keterangan                        |
|----------------------|--------------|------------------------------------|
| rec_usercreated      | varchar(50)  | User pembuat                      |
| rec_userupdate       | varchar(50)  | User update terakhir               |
| rec_datecreated      | datetime     | Tanggal dibuat                     |
| rec_dateupdate       | datetime     | Tanggal update terakhir            |
| rec_status           | char(1)      | Status (A=Active, D=Deleted, I=Inactive) |
| coa_main_code        | varchar(50)  | **Primary Key** - Kode kategori utama |
| coa_main_desc        | varchar(50)  | Deskripsi kategori utama           |
| coa_main_id          | varchar(50)  | ID alternatif                      |
| coa_main_coamain2code| varchar(50)  | Reference code (opsional/legacy)   |
| id                   | int (IDENTITY)| Auto-increment ID                 |
| cek_aktif            | bit          | Flag aktif                         |
| id_h                 | int          | Hierarchy ID (opsional)            |

## Primary Key
- coa_main_code

## Extended Properties
- Deskripsi: "coa level 1 (paling atas)"

## Relasi
- **Parent:** (tidak ada, ini level paling atas)
- **Child:** ms_acc_coasub1 (Level 2) via FK: coasub1_maincode → coa_main_code

## Contoh Data
| coa_main_code | coa_main_desc |
|--------------|--------------|
| 10000        | Asset        |
| 20000        | Liability    |
| 30000        | Equity       |
| 40000        | Revenue      |
| 50000        | Expense      |
