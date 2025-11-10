# Tabel: ms_acc_coasub1

## Deskripsi
Tabel sub kategori pertama COA (Level 2). Digunakan untuk membagi kategori utama menjadi sub kategori yang lebih detail.

## Struktur Kolom
| Kolom             | Tipe         | Keterangan                        |
|-------------------|--------------|------------------------------------|
| rec_usercreated   | varchar(50)  | User pembuat                      |
| rec_userupdate    | varchar(50)  | User update terakhir               |
| rec_datecreated   | datetime     | Tanggal dibuat                     |
| rec_dateupdate    | datetime     | Tanggal update terakhir            |
| rec_status        | char(1)      | Status (A=Active, D=Deleted, I=Inactive) |
| coasub1_code      | varchar(50)  | **Primary Key** - Kode sub kategori 1 |
| coasub1_id        | varchar(50)  | ID alternatif                      |
| coasub1_maincode  | varchar(50)  | FK ke ms_acc_coa_main              |
| coasub1_desc      | varchar(50)  | Deskripsi sub kategori 1           |

## Primary Key
- coasub1_code

## Foreign Key
- coasub1_maincode → ms_acc_coa_main.coa_main_code

## Extended Properties
- Deskripsi FK: "fk dari acc_coa_main"

## Relasi
- **Parent:** ms_acc_coa_main (Level 1)
- **Child:** ms_acc_coasub2 (Level 3) via FK: coasub2_coasub1code → coasub1_code
