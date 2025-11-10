# Table Relationship Documentation
## tr_admin_it_aplikasi_table ⇄ tr_admin_it_aplikasi_table_msg

## Penjelasan Hubungan (Relationship)

### Struktur Relasi

```
┌─────────────────────────────────────────────┐
│ tr_admin_it_aplikasi_table                  │
│ (Model: TableMetadata)                      │
│                                             │
│ PRIMARY KEY: tr_aplikasi_table_id (VARCHAR) │
│                                             │
│ Contoh: TBL-9E9CE29BB1                      │
└─────────────────────────────────────────────┘
                    │
                    │ ONE-TO-MANY
                    │ 
                    ▼
┌──────────────────────────────────────────────────┐
│ tr_admin_it_aplikasi_table_msg                   │
│ (Model: TableMessage)                            │
│                                                  │
│ PRIMARY KEY: tr_admin_it_aplikasi_table_msg_id   │
│ FOREIGN KEY: tr_aplikasi_table_id ───────────────┘
│                                                  
│ Satu table bisa memiliki BANYAK messages         
└──────────────────────────────────────────────────┘
```

**Penjelasan**:
- `tr_aplikasi_table_id` adalah **PRIMARY KEY** di tabel `tr_admin_it_aplikasi_table`
- `tr_aplikasi_table_id` adalah **FOREIGN KEY** di tabel `tr_admin_it_aplikasi_table_msg`
- Hubungan: **ONE-TO-MANY** (satu tabel bisa punya banyak pesan)

## Summary

✅ **Model `TableMetadata` dan `TableMessage` sudah dibuat**
✅ **Relationship ONE-TO-MANY sudah di-setup**
✅ **Foreign Key `tr_aplikasi_table_id` menghubungkan kedua tabel**
✅ **Semua test relationship berhasil**

Sekarang Anda bisa:
- Mengakses pesan dari tabel: `$table->messages`
- Mengakses tabel dari pesan: `$message->tableMetadata`
- Query tabel yang punya pesan: `TableMetadata::withMessages()->get()`
- Query tabel tanpa pesan: `TableMetadata::withoutMessages()->get()`
