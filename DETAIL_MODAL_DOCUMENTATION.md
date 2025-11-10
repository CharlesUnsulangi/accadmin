# Table Detail & Quick Add Message Feature

## Overview

Fitur **Click Row to Detail** memungkinkan user untuk:
1. **Klik baris tabel** untuk membuka detail lengkap
2. **Quick Add Message** langsung dari detail modal
3. Message otomatis **refer ke Primary Key** (`tr_aplikasi_table_id`)
4. **Auto-update** `table_note` setelah add/delete message

## Cara Penggunaan

### 1. Klik Baris Tabel

```
User melihat table list
    ↓
Klik pada baris tabel mana saja
    ↓
Modal detail terbuka
```

**Catatan**: Kolom "Actions" dengan tombol tidak akan trigger detail modal (menggunakan `@click.stop`)

### 2. Detail Modal

Modal detail menampilkan:

**Left Panel - Table Information:**
- Table ID (Primary Key)
- Table Name
- Table Type
- Schema Description
- Total Records
- Date Range (First → Last Record)
- Last Updated

**Right Panel - Quick Add Message:**
- Form textarea untuk input message
- Display Primary Key yang sedang di-refer
- Button "Add Message"
- Current Note (table_note terbaru)

**Bottom Panel - Message History:**
- List semua messages (urut terbaru)
- Badge "Latest" untuk message terakhir
- Button delete untuk setiap message
- Button refresh

### 3. Quick Add Message

#### Proses:

```php
User mengisi textarea
    ↓
Klik "Add Message"
    ↓
API Call: POST /api/database-tables/add-message
Body: {
    tr_aplikasi_table_id: "TBL-9E9CE29BB1",  // PRIMARY KEY
    msg_desc: "Isi message user"
}
    ↓
Backend: TableMessage::addMessage()
    ↓
Message disimpan di tr_admin_it_aplikasi_table_msg
    ↓
table_note di-update dengan message ini
    ↓
Response success
    ↓
UI: Reload messages & table list
    ↓
Success alert: "✅ Message added! Table note updated."
```

#### Primary Key Reference:

```javascript
// Primary Key ditampilkan di UI
<small class="text-muted">
    <i class="fas fa-key me-1"></i>
    Primary Key: <code>TBL-9E9CE29BB1</code>
</small>

// Digunakan saat add message
body: JSON.stringify({
    tr_aplikasi_table_id: this.detailModal.table.tr_aplikasi_table_id, // ✅
    msg_desc: this.detailModal.quickMessage
})
```

## UI Components

### Detail Modal Structure

```blade
<div x-show="detailModal.show" class="modal">
    <div class="modal-dialog modal-xl">
        <!-- Header: Table Name -->
        <div class="modal-header bg-gradient">
            <h5>Table Details: {{ table_name }}</h5>
        </div>
        
        <!-- Body: 3 Sections -->
        <div class="modal-body">
            <div class="row">
                <!-- 1. Table Info (Left) -->
                <div class="col-lg-6">
                    <table>
                        <tr><td>Table ID:</td><td>{{ tr_aplikasi_table_id }}</td></tr>
                        <tr><td>Table Name:</td><td>{{ table_name }}</td></tr>
                        <!-- ... -->
                    </table>
                </div>
                
                <!-- 2. Quick Add Message (Right) -->
                <div class="col-lg-6">
                    <textarea x-model="detailModal.quickMessage"></textarea>
                    <button @click="quickAddMessage()">Add Message</button>
                    <div>Current Note: {{ table_note }}</div>
                </div>
                
                <!-- 3. Message History (Full Width) -->
                <div class="col-12">
                    <div x-for="message in detailModal.messages">
                        {{ message.msg_desc }}
                        <button @click="deleteMessageFromDetail()">Delete</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer: Actions -->
        <div class="modal-footer">
            <button @click="closeDetailModal()">Close</button>
            <button @click="openMessagesModal()">View All Messages</button>
        </div>
    </div>
</div>
```

### Table Row Click Handler

```blade
<template x-for="table in tables">
    <tr @click="viewTableDetail(table)" style="cursor: pointer;">
        <!-- Seluruh row clickable -->
        <td @click.stop>Table Name</td>  <!-- @click.stop = tidak trigger parent -->
        <td @click.stop>Description</td>
        <td @click.stop>Records</td>
        <!-- ... -->
        <td @click.stop>
            <!-- Actions buttons tidak trigger row click -->
            <button @click="updateTableMetadata()">Sync</button>
            <button @click="openMessagesModal()">Messages</button>
        </td>
    </tr>
</template>
```

## Alpine.js State

```javascript
detailModal: {
    show: false,              // Modal visibility
    table: null,              // Current table object (full data)
    messages: [],             // Messages for this table
    quickMessage: '',         // Quick add message input
    submitting: false,        // Submit state
    loadingMessages: false    // Loading messages state
}
```

## Alpine.js Methods

### 1. viewTableDetail(table)

```javascript
viewTableDetail(table) {
    this.detailModal.show = true;
    this.detailModal.table = table;  // Store full table object
    this.detailModal.quickMessage = '';
    this.loadDetailMessages();
}
```

### 2. closeDetailModal()

```javascript
closeDetailModal() {
    this.detailModal.show = false;
    this.detailModal.table = null;
    this.detailModal.messages = [];
    this.detailModal.quickMessage = '';
}
```

### 3. loadDetailMessages()

```javascript
async loadDetailMessages() {
    this.detailModal.loadingMessages = true;
    const response = await fetch(`/api/database-tables/messages/${this.detailModal.table.tr_aplikasi_table_id}`);
    const data = await response.json();
    this.detailModal.messages = data.data;
    this.detailModal.loadingMessages = false;
}
```

### 4. quickAddMessage() - UTAMA

```javascript
async quickAddMessage() {
    // Validation
    if (!this.detailModal.quickMessage.trim()) {
        this.showAlert('warning', 'Please enter a message');
        return;
    }
    
    this.detailModal.submitting = true;
    
    // API Call dengan PRIMARY KEY
    const response = await fetch('/api/database-tables/add-message', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            tr_aplikasi_table_id: this.detailModal.table.tr_aplikasi_table_id, // ✅ PRIMARY KEY
            msg_desc: this.detailModal.quickMessage
        })
    });
    
    const data = await response.json();
    
    if (data.success) {
        // Clear input
        this.detailModal.quickMessage = '';
        
        // Reload messages in detail modal
        await this.loadDetailMessages();
        
        // Reload table list (update table_note)
        this.loadTables();
        
        // Update detail modal table data
        const updatedTable = this.tables.find(t => 
            t.tr_aplikasi_table_id === this.detailModal.table.tr_aplikasi_table_id
        );
        if (updatedTable) {
            this.detailModal.table = updatedTable; // Update with latest table_note
        }
        
        this.showAlert('success', '✅ Message added! Table note updated.');
    }
    
    this.detailModal.submitting = false;
}
```

### 5. deleteMessageFromDetail(messageId)

```javascript
async deleteMessageFromDetail(messageId) {
    if (!confirm('Delete this message?')) return;
    
    const response = await fetch(`/api/database-tables/delete-message/${messageId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    });
    
    const data = await response.json();
    
    if (data.success) {
        // Reload messages
        await this.loadDetailMessages();
        
        // Reload table list
        this.loadTables();
        
        // Update detail modal table data
        const updatedTable = this.tables.find(t => 
            t.tr_aplikasi_table_id === this.detailModal.table.tr_aplikasi_table_id
        );
        if (updatedTable) {
            this.detailModal.table = updatedTable;
        }
        
        this.showAlert('success', '✅ Message deleted! Table note updated.');
    }
}
```

## CSS Styling

```css
.table-hover tbody tr {
    transition: all 0.2s ease;
}

.table-hover tbody tr:hover {
    background-color: rgba(102, 126, 234, 0.05);
    transform: scale(1.01);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.cursor-pointer {
    cursor: pointer !important;
}
```

## User Flow Diagram

```
┌─────────────────────────────────────┐
│ User di halaman Database Tables     │
└─────────────────────────────────────┘
                │
                ▼
┌─────────────────────────────────────┐
│ Klik baris tabel "ms_acc_coa"       │
└─────────────────────────────────────┘
                │
                ▼
┌─────────────────────────────────────┐
│ Detail Modal terbuka                │
│ - Table ID: TBL-9E9CE29BB1          │
│ - Table Name: ms_acc_coa            │
│ - Records: 501                      │
│ - Current Note: "..."               │
└─────────────────────────────────────┘
                │
                ▼
┌─────────────────────────────────────┐
│ User ketik di Quick Add Message:    │
│ "Tabel COA sudah diaudit 2025"      │
└─────────────────────────────────────┘
                │
                ▼
┌─────────────────────────────────────┐
│ Klik "Add Message"                  │
└─────────────────────────────────────┘
                │
                ▼
┌─────────────────────────────────────┐
│ API: POST /add-message              │
│ Body: {                             │
│   tr_aplikasi_table_id:             │
│     "TBL-9E9CE29BB1", ← PRIMARY KEY │
│   msg_desc: "Tabel COA..."          │
│ }                                   │
└─────────────────────────────────────┘
                │
                ▼
┌─────────────────────────────────────┐
│ Backend: TableMessage::addMessage() │
│ - Save to table_msg                 │
│ - Update table_note                 │
└─────────────────────────────────────┘
                │
                ▼
┌─────────────────────────────────────┐
│ UI Updates:                         │
│ ✓ Message history refreshed         │
│ ✓ Current Note updated              │
│ ✓ Table list badge updated          │
│ ✓ Success alert shown               │
└─────────────────────────────────────┘
```

## Benefits

### 1. **Quick Access** ⚡
User tidak perlu klik tombol Messages dulu, langsung klik row → detail modal.

### 2. **Context Aware** 🎯
Primary Key (`tr_aplikasi_table_id`) otomatis ter-refer, user tidak perlu tahu ID-nya.

### 3. **Instant Feedback** 💬
User langsung lihat current note dan history messages di satu tempat.

### 4. **Auto-Sync** 🔄
table_note otomatis update setelah add/delete message.

### 5. **Better UX** ✨
- Hover effect pada row
- Loading states
- Success/error alerts
- Smooth transitions

## Testing

### Manual Testing Steps:

1. **Buka halaman Database Tables**
   ```
   http://localhost/database-tables
   ```

2. **Hover mouse pada baris tabel**
   - Row harus highlight
   - Cursor berubah jadi pointer

3. **Klik pada baris tabel**
   - Detail modal harus terbuka
   - Data tabel harus tampil lengkap
   - Primary Key harus terlihat

4. **Isi Quick Add Message**
   - Ketik message: "Test message dari detail modal"
   - Klik "Add Message"
   - Alert success harus muncul
   - Message history harus refresh
   - Current Note harus update

5. **Cek table list**
   - Table note badge harus update dengan message terbaru

6. **Delete message dari detail**
   - Klik delete pada message
   - Confirm
   - Message harus hilang
   - Current Note harus update ke message sebelumnya

7. **Klik "View All Messages"**
   - Harus buka Messages modal yang lengkap
   - Detail modal harus close

## Integration dengan Fitur Lain

### 1. Messages Modal
Detail modal punya button "View All Messages" yang membuka messages modal penuh.

### 2. Table Note Sync
Quick add message menggunakan `TableMessage::addMessage()` yang otomatis update `table_note`.

### 3. Table Metadata
Detail modal menampilkan semua metadata dari `tr_admin_it_aplikasi_table`.

## Troubleshooting

### Modal tidak muncul saat klik row?
- Periksa console untuk error JavaScript
- Pastikan Alpine.js ter-load
- Cek method `viewTableDetail()` dipanggil

### Primary Key tidak ter-refer?
- Debug: `console.log(this.detailModal.table.tr_aplikasi_table_id)`
- Pastikan table object lengkap dari API

### table_note tidak update?
- Pastikan `loadTables()` dipanggil setelah add message
- Cek backend `updateTableNote()` method

---

**Feature Status**: ✅ Complete  
**Primary Key Reference**: ✅ Implemented  
**Auto-Sync**: ✅ Working  
**UI/UX**: ✅ Enhanced
