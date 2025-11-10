# Table Note Auto-Sync Feature

## Konsep

Ketika user menambahkan atau menghapus **message** di tabel `tr_admin_it_aplikasi_table_msg`, sistem akan **otomatis mengupdate** field `table_note` di tabel `tr_admin_it_aplikasi_table` dengan **message terakhir**.

## Cara Kerja

```
┌─────────────────────────────────────────────┐
│ User Action                                 │
└─────────────────────────────────────────────┘
                    │
                    ▼
┌─────────────────────────────────────────────┐
│ Add/Delete Message di                       │
│ tr_admin_it_aplikasi_table_msg              │
└─────────────────────────────────────────────┘
                    │
                    ▼
┌─────────────────────────────────────────────┐
│ Trigger: updateTableNote()                  │
└─────────────────────────────────────────────┘
                    │
                    ▼
┌─────────────────────────────────────────────┐
│ Update table_note di                        │
│ tr_admin_it_aplikasi_table                  │
│ dengan message TERAKHIR                     │
└─────────────────────────────────────────────┘
```

## Implementasi di Model

### TableMessage Model (`app/Models/TableMessage.php`)

#### 1. Method `addMessage()` - Dengan Auto-Sync

```php
public static function addMessage($tableId, $message, $user = null)
{
    // Create the message
    $newMessage = self::create([
        'tr_admin_it_aplikasi_table_msg_id' => self::getNextId(),
        'tr_aplikasi_table_id' => $tableId,
        'msg_desc' => $message,
        'user_created' => $user ?? auth()->user()?->name ?? 'System',
        'date_created' => now()
    ]);
    
    // 🔄 AUTO-SYNC: Update table_note dengan message ini
    self::updateTableNote($tableId, $message);
    
    return $newMessage;
}
```

#### 2. Method `updateTableNote()` - Sinkronisasi Logic

```php
public static function updateTableNote($tableId, $message = null)
{
    // Jika message tidak diberikan, ambil message terakhir
    if (!$message) {
        $latestMessage = self::where('tr_aplikasi_table_id', $tableId)
            ->orderBy('date_created', 'desc')
            ->first();
        
        $message = $latestMessage ? $latestMessage->msg_desc : null;
    }
    
    // Update table_note di tr_admin_it_aplikasi_table
    DB::table('tr_admin_it_aplikasi_table')
        ->where('tr_aplikasi_table_id', $tableId)
        ->update([
            'table_note' => $message,
            'date_updated' => now()
        ]);
}
```

#### 3. Method `delete()` - Override dengan Auto-Sync

```php
public function delete()
{
    $tableId = $this->tr_aplikasi_table_id;
    
    // Hapus message
    $deleted = parent::delete();
    
    // 🔄 AUTO-SYNC: Update table_note dengan message terakhir yang baru
    // (atau null jika tidak ada message lagi)
    self::updateTableNote($tableId);
    
    return $deleted;
}
```

## Skenario Penggunaan

### Skenario 1: Menambah Message Pertama

**Kondisi Awal:**
- Tabel: `ms_acc_coa`
- `table_note`: (kosong/null)
- Messages: 0

**Action:**
```php
TableMessage::addMessage('TBL-9E9CE29BB1', 'Ini adalah COA utama');
```

**Hasil:**
- `table_note`: "Ini adalah COA utama" ✅
- Messages: 1

---

### Skenario 2: Menambah Message Kedua

**Kondisi Awal:**
- `table_note`: "Ini adalah COA utama"
- Messages: 1

**Action:**
```php
TableMessage::addMessage('TBL-9E9CE29BB1', 'Update: Sudah diaudit');
```

**Hasil:**
- `table_note`: "Update: Sudah diaudit" ✅ (diganti dengan yang terbaru)
- Messages: 2

---

### Skenario 3: Menambah Message Ketiga

**Kondisi Awal:**
- `table_note`: "Update: Sudah diaudit"
- Messages: 2

**Action:**
```php
TableMessage::addMessage('TBL-9E9CE29BB1', 'Perbaikan struktur 2025-11-10');
```

**Hasil:**
- `table_note`: "Perbaikan struktur 2025-11-10" ✅
- Messages: 3

---

### Skenario 4: Menghapus Message Terakhir

**Kondisi Awal:**
- `table_note`: "Perbaikan struktur 2025-11-10"
- Messages: 3 (Msg1, Msg2, Msg3)

**Action:**
```php
$msg3 = TableMessage::find(3);
$msg3->delete();
```

**Hasil:**
- `table_note`: "Update: Sudah diaudit" ✅ (kembali ke Msg2)
- Messages: 2 (Msg1, Msg2)

---

### Skenario 5: Menghapus Semua Messages

**Kondisi Awal:**
- `table_note`: "Ini adalah COA utama"
- Messages: 1 (Msg1)

**Action:**
```php
$msg1 = TableMessage::find(1);
$msg1->delete();
```

**Hasil:**
- `table_note`: null ✅ (kosong karena tidak ada message)
- Messages: 0

## Frontend Integration (Alpine.js)

### Auto-Reload Table List

Ketika user menambah atau hapus message, table list akan **auto-reload** untuk menampilkan `table_note` yang ter-update:

```javascript
async addMessage() {
    // ... add message via API ...
    
    if (data.success) {
        this.messagesModal.newMessage = '';
        await this.loadMessages();
        this.loadTables(); // ✅ Reload table list
        this.showAlert('success', 'Message added. Table note updated.');
    }
}

async deleteMessage(messageId) {
    // ... delete message via API ...
    
    if (data.success) {
        await this.loadMessages();
        this.loadTables(); // ✅ Reload table list
        this.showAlert('success', 'Message deleted. Table note updated.');
    }
}
```

### Display table_note di UI

```blade
<template x-for="table in tables">
    <tr>
        <td>
            <strong>{{ table.table_name }}</strong>
            <br>
            <small>ID: {{ table.tr_aplikasi_table_id }}</small>
            
            <!-- Display table_note (latest message) -->
            <template x-if="table.table_note">
                <br>
                <span class="badge bg-info">
                    <i class="fas fa-sticky-note"></i>
                    {{ table.table_note }}
                </span>
            </template>
        </td>
    </tr>
</template>
```

## Testing

### Test Script: `test_table_note_sync.php`

```bash
php test_table_note_sync.php
```

**Expected Output:**
```
✓ Message added: "Pesan pertama"
✓ table_note sekarang: "Pesan pertama"

✓ Message added: "Pesan kedua"
✓ table_note sekarang: "Pesan kedua"

✓ Message deleted
✓ table_note sekarang: "Pesan pertama"

✓ All messages deleted
✓ table_note sekarang: (empty)
```

## Benefits

### 1. **Quick View** 📋
User bisa melihat catatan terbaru langsung di table list tanpa perlu buka modal messages.

### 2. **Always Up-to-Date** 🔄
`table_note` selalu sinkron dengan message terakhir, tidak perlu manual update.

### 3. **Historical Tracking** 📜
Semua message history tetap tersimpan di `tr_admin_it_aplikasi_table_msg`.

### 4. **Automatic** ⚡
Tidak perlu coding tambahan, cukup gunakan `addMessage()` dan `delete()`.

### 5. **Rollback Support** ⏮️
Ketika message dihapus, `table_note` otomatis rollback ke message sebelumnya.

## Database Schema Impact

### tr_admin_it_aplikasi_table

Field yang ter-update:
- `table_note` - Isi message terakhir
- `date_updated` - Timestamp update terakhir

### tr_admin_it_aplikasi_table_msg

Semua messages tetap tersimpan dengan lengkap:
- Full message history
- User tracking
- Date tracking

## Example Workflow

### User Perspective

1. **User buka halaman Database Tables**
   - Lihat table list dengan `table_note` terbaru

2. **User klik tombol Messages 💬**
   - Modal terbuka
   - Lihat semua historical messages

3. **User add new message**
   - Ketik message
   - Klik "Add Message"
   - ✅ Success alert: "Message added. Table note updated."
   - Table list auto-refresh
   - Badge `table_note` langsung update dengan message baru

4. **User delete old message**
   - Klik delete pada message lama
   - Confirm
   - ✅ Success alert: "Message deleted. Table note updated."
   - Table list auto-refresh
   - Badge `table_note` tetap menampilkan message terbaru yang tersisa

## Cleanup Scripts

### Check Current State
```bash
php check_current_state.php
```

### Cleanup Test Messages
```bash
php cleanup_test_messages.php
```

## API Response Examples

### Add Message Response
```json
{
  "success": true,
  "message": "Message added successfully",
  "data": {
    "tr_admin_it_aplikasi_table_msg_id": 5,
    "tr_aplikasi_table_id": "TBL-9E9CE29BB1",
    "msg_desc": "New important note",
    "user_created": "Admin",
    "date_created": "2025-11-10"
  }
}
```

**Side Effect:** `tr_admin_it_aplikasi_table.table_note` updated to "New important note"

### Delete Message Response
```json
{
  "success": true,
  "message": "Message deleted successfully"
}
```

**Side Effect:** `tr_admin_it_aplikasi_table.table_note` updated to previous latest message (or null)

## Troubleshooting

### table_note tidak update?

**Check:**
1. Apakah `updateTableNote()` dipanggil?
2. Apakah `tr_aplikasi_table_id` benar?
3. Apakah ada error di log?

**Debug:**
```php
$table = TableMetadata::findByTableId('TBL-9E9CE29BB1');
echo "Current table_note: " . $table->table_note;

$latest = TableMessage::where('tr_aplikasi_table_id', 'TBL-9E9CE29BB1')
    ->orderBy('date_created', 'desc')
    ->first();
echo "Latest message: " . ($latest ? $latest->msg_desc : 'None');
```

### Frontend tidak refresh?

**Check:**
```javascript
// Pastikan loadTables() dipanggil setelah add/delete
this.loadTables();
```

---

**Feature Completed**: ✅  
**Auto-Sync**: ✅  
**Tested**: ✅  
**Documented**: ✅
