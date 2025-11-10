# Closing History - Dual View Mode Documentation

## Overview
Fitur dual view mode untuk melihat data closing dalam dua format berbeda:
1. **Detail View** - Tampilan detail per COA (flat list)
2. **Group View** - Tampilan terkelompok berdasarkan hierarchy COA (tree structure)

## Features

### 1. View Mode Tabs
Dua tab untuk switch antara view mode:

#### Detail View Tab
- Icon: `fa-list`
- Tampilan: Flat table dengan semua COA
- Features:
  - Individual row expand/collapse untuk hierarchy
  - Toggle all expand/collapse
  - Filter by type, year, month, status
  - Export to Excel

#### Group View Tab  
- Icon: `fa-sitemap`
- Tampilan: Tree structure dengan 4 level hierarchy
- Features:
  - Collapsible hierarchy tree
  - Automatic totals calculation per level
  - Expand all / Collapse all buttons
  - Export to Excel

### 2. Group View (Hierarchy Tree)

#### Level 1: Main Category (ms_acc_coa_main)
- **Style**: `table-primary` (blue background)
- **Icon**: `fa-folder` (primary color)
- **Display**: 
  ```
  [chevron] 🗂️ [code] Description
  ```
- **Totals**: Sum of all sub-categories
- **Collapsible**: Click chevron to show/hide sub1 categories

#### Level 2: Sub Category 1 (ms_acc_coasub1)
- **Style**: `table-info` (cyan background)
- **Icon**: `fa-folder-open` (info color)
- **Indent**: `ps-4` (padding-left: 1.5rem)
- **Display**:
  ```
      [chevron] 📂 [code] Description
  ```
- **Totals**: Sum of all sub2 categories under this sub1
- **Collapsible**: Click chevron to show/hide sub2 categories

#### Level 3: Sub Category 2 (ms_acc_coasub2)
- **Style**: `table-warning` (yellow background)
- **Icon**: `fa-file-alt` (warning color)
- **Indent**: `ps-5` (padding-left: 3rem)
- **Display**:
  ```
          [chevron] 📄 [code] Description
  ```
- **Totals**: Sum of all COA under this sub2
- **Collapsible**: Click chevron to show/hide COA details

#### Level 4: COA Detail (ms_acc_coa)
- **Style**: Default (white background)
- **Icon**: `fa-file` (secondary color)
- **Indent**: `ps-5` + `ms-4` (extra indent)
- **Display**:
  ```
              📄 [code] Description
  ```
- **Values**: Individual COA amounts
- **Non-collapsible**: Leaf node

### 3. Hierarchy Tree Structure

```
📁 1 - Aktiva (Main)                    [Total Main]
    📂 101 - Aktiva Lancar (Sub1)       [Total Sub1]
        📄 10101 - Kas (Sub2)           [Total Sub2]
            📄 1010101 - Kas Kecil      [Individual amounts]
            📄 1010102 - Kas Besar      [Individual amounts]
        📄 10102 - Bank (Sub2)          [Total Sub2]
            📄 1010201 - Bank BRI       [Individual amounts]
            📄 1010202 - Bank BNI       [Individual amounts]
    📂 102 - Aktiva Tetap (Sub1)        [Total Sub1]
        📄 10201 - Tanah (Sub2)         [Total Sub2]
            📄 1020101 - Tanah Kantor   [Individual amounts]
```

### 4. Data Grouping Algorithm

```javascript
buildGroupedData() {
    // Group by: Main → Sub1 → Sub2 → COA
    // Calculate totals at each level
    // Handle NULL values for hierarchy
}
```

**Process:**
1. Loop through all data items
2. Group by `coa_main_code` (create if not exists)
3. Within main, group by `coasub1_code`
4. Within sub1, group by `coasub2_code`
5. Within sub2, add individual COA items
6. Calculate running totals:
   - Sub2 totals = Sum of COA items
   - Sub1 totals = Sum of Sub2 totals
   - Main totals = Sum of Sub1 totals

**NULL Handling:**
- If `coa_main_code` is NULL → Use "NULL" as key, "Tidak Ada Main Category" as description
- Same for Sub1 and Sub2

### 5. Expand/Collapse System

#### State Management
```javascript
expandedGroups: {
    main: {},      // { '1': true, '2': false, ... }
    sub1: {},      // { '1-101': true, '1-102': false, ... }
    sub2: {}       // { '1-101-10101': true, ... }
}
```

#### Toggle Functions
- **Individual Toggle**: `toggleGroup(level, key)`
  - Toggle specific group at any level
  - Key format:
    - Main: `mainCode`
    - Sub1: `mainCode-sub1Code`
    - Sub2: `mainCode-sub1Code-sub2Code`

- **Expand All**: `expandAllGroups()`
  - Set all groups to expanded state
  - Loops through all levels

- **Collapse All**: `collapseAllGroups()`
  - Reset all groups to collapsed state

### 6. Column Structure (Group View)

| Column | Width | Alignment | Content |
|--------|-------|-----------|---------|
| # | 50px | Center | Chevron toggle button |
| Hierarchy / COA | Auto | Left | Code + Description (indented) |
| Opening Debet | 150px | Right | Number formatted |
| Opening Kredit | 150px | Right | Number formatted |
| Mutasi Debet | 150px | Right | Number formatted |
| Mutasi Kredit | 150px | Right | Number formatted |
| Closing Debet | 150px | Right | Number formatted (bold) |
| Closing Kredit | 150px | Right | Number formatted (bold) |
| Transaksi | 100px | Center | Badge with count |

### 7. Visual Indicators

#### Color Coding
- **Main Category**: Blue (`table-primary`, `text-primary`)
- **Sub1 Category**: Cyan (`table-info`, `text-info`)
- **Sub2 Category**: Yellow (`table-warning`, `text-warning`)
- **COA Detail**: Gray (`text-secondary`)

#### Icons
- **Main**: 🗂️ `fa-folder`
- **Sub1**: 📂 `fa-folder-open`
- **Sub2**: 📄 `fa-file-alt`
- **COA**: 📄 `fa-file`
- **Expand**: ▶️ `fa-chevron-right`
- **Collapse**: 🔽 `fa-chevron-down`

### 8. Totals Display

#### Per-Level Totals
Each hierarchy level shows aggregated totals:
- All numeric columns are summed
- Transaction count is summed
- Bold font for closing amounts

#### Grand Total (Footer)
- Sum of all Main Categories
- Displayed in table footer
- Same as Detail View grand total

## Technical Implementation

### File Modified
- `resources/views/closing-history.blade.php`

### Key Components

#### 1. Tab Navigation (Lines ~90-115)
```blade
<ul class="nav nav-tabs" role="tablist">
    <li class="nav-item">
        <button :class="viewMode === 'detail' ? 'active' : ''"
                @click="viewMode = 'detail'">
            Detail View
        </button>
    </li>
    <li class="nav-item">
        <button :class="viewMode === 'group' ? 'active' : ''"
                @click="viewMode = 'group'">
            Group View (Hierarchy)
        </button>
    </li>
</ul>
```

#### 2. Conditional View Rendering
```blade
<!-- Detail View -->
<div x-show="!loading && data.length > 0 && viewMode === 'detail'">
    <!-- Existing detail table -->
</div>

<!-- Group View -->
<div x-show="!loading && data.length > 0 && viewMode === 'group'">
    <!-- New hierarchical table -->
</div>
```

#### 3. Alpine.js State Variables
```javascript
viewMode: 'detail',      // Current active view
groupedData: {},         // Hierarchical data structure
expandedGroups: {        // Expand/collapse state
    main: {},
    sub1: {},
    sub2: {}
}
```

#### 4. Data Processing
```javascript
// After loading data from API
this.buildGroupedData();  // Transform flat data to hierarchy
```

## User Experience

### Navigation Flow
1. User opens `/closing/history`
2. Default: Detail View is active
3. User clicks "Group View (Hierarchy)" tab
4. View switches to hierarchical tree
5. User can:
   - Click individual chevrons to expand/collapse
   - Click "Expand All" to see full tree
   - Click "Collapse All" to minimize view
   - Switch back to Detail View anytime

### Use Cases

#### Detail View - Best For:
- Searching specific COA
- Viewing all COA in one list
- Quick scanning of all accounts
- Simple filtering and sorting

#### Group View - Best For:
- Understanding account structure
- Analyzing category totals
- Financial statement preparation
- Hierarchical reporting
- Balance sheet grouping

## Performance Considerations

### Data Processing
- `buildGroupedData()` runs only once per data load
- O(n) complexity for grouping
- Minimal memory overhead

### DOM Rendering
- Alpine.js conditional rendering (`x-show`, `x-if`)
- Only expanded nodes are rendered
- Smooth transitions with minimal re-renders

### Optimization Tips
1. Use `x-if` for large trees (removes DOM elements)
2. Use `x-show` for small trees (toggles visibility)
3. Lazy load COA details on expand
4. Implement virtual scrolling for 1000+ items

## Browser Compatibility
- ✅ Chrome/Edge (Chromium)
- ✅ Firefox
- ✅ Safari
- ⚠️ IE11 not supported (Alpine.js requires modern JS)

## Future Enhancements

### Planned Features
1. **Search in Group View**: Highlight and auto-expand matching nodes
2. **Export Group View**: Excel with hierarchy levels
3. **Print Group View**: Formatted for printing with indentation
4. **Drag & Drop**: Reorder categories
5. **Custom Grouping**: Group by period, status, or custom fields
6. **Save View Preference**: Remember user's preferred view mode
7. **Bookmark Expanded State**: Persist expand/collapse state

### Advanced Features
1. **Lazy Loading**: Load sub-levels on demand for huge datasets
2. **Virtual Scrolling**: Handle 10,000+ COA efficiently
3. **Keyboard Navigation**: Arrow keys to navigate tree
4. **Multi-select**: Select multiple nodes for bulk operations
5. **Context Menu**: Right-click for quick actions

## Testing Checklist

### Detail View
- [x] Tab switches to Detail View
- [x] All COA displayed in flat list
- [x] Individual expand/collapse works
- [x] Toggle all expand/collapse works
- [x] Filters apply correctly

### Group View
- [x] Tab switches to Group View
- [x] Hierarchy structure correct (Main → Sub1 → Sub2 → COA)
- [x] Totals calculated correctly at each level
- [x] Grand total matches Detail View
- [x] Color coding applied (blue → cyan → yellow → gray)
- [x] Icons displayed correctly
- [x] Indentation shows hierarchy levels
- [x] Chevron toggle works at all levels
- [x] Expand All button works
- [x] Collapse All button works
- [x] NULL values handled properly

### Data Integrity
- [ ] Totals match between views
- [ ] No data loss when switching views
- [ ] Filters affect both views equally
- [ ] Export works from both views

## Related Files
- `resources/views/closing-history.blade.php` - Main view file
- `app/Http/Controllers/ClosingProcessController.php` - Data source
- `app/Services/ClosingService.php` - Closing calculation

## API Reference

### Endpoints Used
- `GET /closing/history/data` - Fetch closing data
  - Parameters: `type`, `year`, `month`, `status`
  - Returns: `{ success: true, data: [...] }`

### Data Structure
```json
{
  "id": 1,
  "coa_code": "1010101",
  "coa_desc": "Kas Kecil",
  "coa_main_code": "1",
  "coa_main_desc": "Aktiva",
  "coasub1_code": "101",
  "coasub1_desc": "Aktiva Lancar",
  "coasub2_code": "10101",
  "coasub2_desc": "Kas",
  "opening_debet": 1000000.00,
  "opening_kredit": 0.00,
  "mutasi_debet": 500000.00,
  "mutasi_kredit": 300000.00,
  "closing_debet": 1200000.00,
  "closing_kredit": 0.00,
  "jumlah_transaksi": 15
}
```

## Version History
- **v1.0** (Nov 11, 2025) - Initial implementation
  - Detail View with expand/collapse
  - Group View with 4-level hierarchy
  - Tab-based view switching
  - Expand all / Collapse all functionality

## Support
For issues or feature requests, refer to project repository.
