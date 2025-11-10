# Closing History - Collapsible Hierarchy Feature

## Overview
Fitur collapse/expand untuk menampilkan hierarchy descriptions (Main Category, Sub Category 1, Sub Category 2) pada halaman Closing History. Fitur ini membuat tampilan lebih ringkas dan user-friendly.

## Features

### 1. Individual Row Collapse
- **Toggle Button**: Setiap row memiliki tombol chevron (▶/▼) di kolom "COA Description"
- **Show/Hide**: Klik tombol untuk expand/collapse hierarchy details
- **Icon Indicator**: 
  - `bi-chevron-right` (▶) = Collapsed
  - `bi-chevron-down` (▼) = Expanded
- **Conditional Display**: Tombol hanya muncul jika COA memiliki hierarchy (main/sub1/sub2)

### 2. Expand/Collapse All
- **Master Toggle**: Tombol di header table kolom "COA Description"
- **Bulk Action**: Expand atau collapse semua rows sekaligus
- **State Tracking**: Variable `allExpanded` untuk track status global

### 3. Hierarchy Details Display
Saat expanded, menampilkan:

#### Main Category
- Label: "Main Category:"
- Format: `[coa_main_code] coa_main_desc`
- Color: Primary (blue)

#### Sub Category 1
- Label: "Sub Category 1:"
- Format: `[coasub1_code] coasub1_desc`
- Color: Info (cyan)

#### Sub Category 2
- Label: "Sub Category 2:"
- Format: `[coasub2_code] coasub2_desc`
- Color: Warning (yellow)

### 4. Smooth Transitions
- **Alpine.js Transitions**: Menggunakan `x-transition` untuk animasi smooth
- **Enter Animation**: Fade in + scale up (200ms)
- **Leave Animation**: Fade out + scale down (150ms)

## Technical Implementation

### File Modified
- `resources/views/closing-history.blade.php`

### Key Changes

#### 1. Table Header (Lines ~118-131)
```blade
<th>
    COA Description
    <button @click="toggleAllHierarchy()" class="btn btn-sm btn-link p-0 ms-2" type="button">
        <i class="bi" :class="allExpanded ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
    </button>
</th>
```
- Removed separate columns for Main/Sub1/Sub2
- Added master toggle button in header

#### 2. Table Body (Lines ~134-195)
```blade
<td>
    <div class="d-flex align-items-start">
        <button 
            @click="item.hierarchyExpanded = !item.hierarchyExpanded" 
            class="btn btn-sm btn-link p-0 me-2" 
            type="button"
            x-show="item.coa_main_desc || item.coasub1_desc || item.coasub2_desc">
            <i class="bi" :class="item.hierarchyExpanded ? 'bi-chevron-down' : 'bi-chevron-right'"></i>
        </button>
        <div class="flex-grow-1">
            <div x-text="item.coa_desc" class="text-truncate" :title="item.coa_desc"></div>
            
            <!-- Hierarchy Details - Collapsible -->
            <div x-show="item.hierarchyExpanded" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95"
                 class="mt-2 pt-2 border-top">
                
                <!-- Main Category -->
                <div x-show="item.coa_main_desc" class="mb-2">
                    <small class="text-muted d-block">Main Category:</small>
                    <div class="ms-2">
                        <code class="text-primary" x-text="item.coa_main_code"></code>
                        <span class="ms-1" x-text="item.coa_main_desc"></span>
                    </div>
                </div>
                
                <!-- Sub Category 1 -->
                <div x-show="item.coasub1_desc" class="mb-2">
                    <small class="text-muted d-block">Sub Category 1:</small>
                    <div class="ms-2">
                        <code class="text-info" x-text="item.coasub1_code"></code>
                        <span class="ms-1" x-text="item.coasub1_desc"></span>
                    </div>
                </div>
                
                <!-- Sub Category 2 -->
                <div x-show="item.coasub2_desc" class="mb-2">
                    <small class="text-muted d-block">Sub Category 2:</small>
                    <div class="ms-2">
                        <code class="text-warning" x-text="item.coasub2_code"></code>
                        <span class="ms-1" x-text="item.coasub2_desc"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</td>
```

#### 3. Alpine.js Component Updates

##### Added State Variable
```javascript
allExpanded: false
```

##### Modified loadData() Method
```javascript
if (result.success) {
    // Initialize hierarchyExpanded property for each item
    this.data = result.data.map(item => ({
        ...item,
        hierarchyExpanded: false
    }));
    this.calculateSummary();
}
```

##### Added toggleAllHierarchy() Method
```javascript
toggleAllHierarchy() {
    this.allExpanded = !this.allExpanded;
    this.data = this.data.map(item => ({
        ...item,
        hierarchyExpanded: this.allExpanded
    }));
}
```

## User Interface

### Before (Expanded View)
```
| # | Periode | COA Code | COA Description | Main Category | Sub Category 1 | Sub Category 2 | Opening Debet | ... |
|---|---------|----------|----------------|---------------|----------------|----------------|---------------|-----|
| 1 | 2025    | 1010101  | Kas Kecil      | 1 Aktiva      | 101 Aktiva... | 10101 Kas...  | 1,000,000.00  | ... |
```
- **Problem**: Terlalu banyak kolom, tabel sangat lebar, scroll horizontal

### After (Collapsed View - Default)
```
| # | Periode | COA Code | COA Description [▼] | Opening Debet | ... |
|---|---------|----------|---------------------|---------------|-----|
| 1 | 2025    | 1010101  | ▶ Kas Kecil        | 1,000,000.00  | ... |
```
- **Benefit**: Kompak, mudah dibaca, tidak perlu scroll horizontal

### After (Expanded View - On Click)
```
| # | Periode | COA Code | COA Description [▲] | Opening Debet | ... |
|---|---------|----------|---------------------|---------------|-----|
| 1 | 2025    | 1010101  | ▼ Kas Kecil        | 1,000,000.00  | ... |
|   |         |          |   Main Category:    |               |     |
|   |         |          |   1 Aktiva         |               |     |
|   |         |          |   Sub Category 1:   |               |     |
|   |         |          |   101 Aktiva Lancar|               |     |
|   |         |          |   Sub Category 2:   |               |     |
|   |         |          |   10101 Kas        |               |     |
```
- **Benefit**: Detail hierarchy muncul dalam row yang sama, indented untuk clarity

## Benefits

### 1. Better UX
- ✅ Compact default view
- ✅ Details on demand
- ✅ Smooth animations
- ✅ Visual hierarchy with colors

### 2. Performance
- ✅ All data loaded once
- ✅ No additional API calls
- ✅ Pure client-side toggle
- ✅ Minimal DOM manipulation

### 3. Maintainability
- ✅ Pure Alpine.js (no jQuery)
- ✅ Reactive data binding
- ✅ Clear separation of concerns
- ✅ Easy to extend

## Browser Compatibility
- ✅ Modern browsers (Chrome, Firefox, Edge, Safari)
- ✅ Bootstrap 5 Icons required
- ✅ Alpine.js 3.x required

## Future Enhancements
1. **Remember State**: Save expand/collapse state to localStorage
2. **Keyboard Navigation**: Arrow keys to expand/collapse
3. **Search Highlight**: Keep expanded when searching
4. **Export**: Include hierarchy in Excel export
5. **Print View**: Expand all for printing

## Testing Checklist
- [ ] Toggle individual row expand/collapse
- [ ] Toggle all rows via header button
- [ ] Check animation smoothness
- [ ] Verify button visibility (only show if hierarchy exists)
- [ ] Test with different screen sizes
- [ ] Verify color coding (primary/info/warning)
- [ ] Check data accuracy in expanded view

## Related Files
- `resources/views/closing-history.blade.php` - Main view file
- `app/Http/Controllers/ClosingProcessController.php` - Data source
- `app/Services/ClosingService.php` - Hierarchy data calculation

## Version
- Created: November 11, 2025
- Alpine.js Version: 3.x
- Bootstrap Version: 5.3
