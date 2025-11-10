# Tabel: ms_acc_coasub2

## Deskripsi
Tabel sub kategori kedua COA (Level 3). Digunakan untuk membagi sub kategori 1 menjadi bagian yang lebih detail.

## Struktur Kolom
| Kolom               | Tipe         | Keterangan                        |
|---------------------|--------------|------------------------------------|
| rec_usercreated     | varchar(50)  | User pembuat                      |
| rec_userupdate      | varchar(50)  | User update terakhir               |
| rec_datecreated     | datetime     | Tanggal dibuat                     |
| rec_dateupdate      | datetime     | Tanggal update terakhir            |
| rec_status          | char(1)      | Status (A=Active, D=Deleted, I=Inactive) |
| coasub2_code        | varchar(50)  | **Primary Key** - Kode sub kategori 2 |
| coasub2_id          | varchar(50)  | ID alternatif                      |
| coasub2_coasub1code | varchar(50)  | FK ke ms_acc_coasub1               |
| coasub2_desc        | varchar(50)  | Deskripsi sub kategori 2           |

## Primary Key
- coasub2_code

## Foreign Key
- coasub2_coasub1code → ms_acc_coasub1.coasub1_code

## Extended Properties
- Deskripsi FK: "fk dari ms_acc_coasub1"

## Relasi
- **Parent:** ms_acc_coasub1 (Level 2)
- **Child:** ms_acc_coa (Level 4) via FK: coa_coasub2code → coasub2_code
