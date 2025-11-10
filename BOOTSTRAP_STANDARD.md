# Bootstrap 5 Standard - AccAdmin Project

## Overview
Project ini menggunakan **Bootstrap 5** sebagai UI framework standar untuk semua halaman dan komponen.

## Alasan Pemilihan Bootstrap 5

### Keunggulan Utama
1. **Collapsible Components**
   - Built-in collapse/accordion tanpa JavaScript tambahan
   - Perfect untuk sidebar menu, table rows, dan data hierarchy
   - Mobile-responsive dan accessible

2. **Komponen Lengkap**
   - Cards, Modals, Dropdowns, Tabs, Tooltips
   - Form controls dengan styling konsisten
   - Navigation components (navbar, breadcrumb, pagination)

3. **Grid System Powerful**
   - 12-column responsive grid
   - Breakpoints: xs, sm, md, lg, xl, xxl
   - Auto-layout dan alignment utilities

4. **Utility Classes**
   - Spacing: m-*, p-*, gap-*
   - Colors: text-*, bg-*, border-*
   - Typography: fw-*, fs-*, text-*
   - Display: d-*, flex-*, align-*, justify-*

## Standard Components

### 1. Page Layout
```blade
<div class="container-fluid mt-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col">
            <h2 class="h3">Page Title</h2>
            <p class="text-muted">Page description</p>
        </div>
        <div class="col-auto">
            <button class="btn btn-primary">Action</button>
        </div>
    </div>
    
    <!-- Content -->
    <div class="row">
        <div class="col-12">
            <!-- Your content here -->
        </div>
    </div>
</div>
```

### 2. Statistics Cards
```blade
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-primary">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-icon me-2"></i>Card Title
            </div>
            <div class="card-body">
                <h3 class="card-title text-primary">{{ $count }}</h3>
                <p class="card-text text-muted small">Description</p>
            </div>
        </div>
    </div>
    <!-- Repeat for other cards with different colors -->
</div>
```

### 3. Filters Section
```blade
<div class="card mb-4">
    <div class="card-header bg-light">
        <i class="fas fa-filter me-2"></i>Filters & Search
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Search</label>
                <input type="text" wire:model.live="search" class="form-control" placeholder="Search...">
            </div>
            <div class="col-md-3">
                <label class="form-label">Filter 1</label>
                <select wire:model.live="filter1" class="form-select">
                    <option value="">All</option>
                </select>
            </div>
        </div>
    </div>
</div>
```

### 4. Data Table
```blade
<div class="card mb-4">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="fas fa-table me-2"></i>Table Title</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Column 1</th>
                        <th>Column 2</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td>{{ $item->field }}</td>
                            <td>{{ $item->field2 }}</td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <p>No data found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-light">
        {{ $items->links('pagination::bootstrap-5') }}
    </div>
</div>
```

### 5. Collapsible Sidebar Menu
```blade
<!-- In sidebar -->
<li class="nav-item" x-data="{ open: false }">
    <a href="#" @click.prevent="open = !open" class="nav-link">
        <i class="fas fa-icon me-2"></i>
        <span>Menu Group</span>
        <i class="fas fa-chevron-down ms-auto" :class="{ 'rotate-180': open }"></i>
    </a>
    <ul x-show="open" x-collapse class="nav flex-column ms-3">
        <li class="nav-item">
            <a href="{{ route('route.name') }}" class="nav-link">Submenu</a>
        </li>
    </ul>
</li>
```

### 6. Collapsible Table Rows (Accordion)
```blade
<div class="accordion" id="accordionExample">
    @foreach($items as $index => $item)
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" 
                        data-bs-toggle="collapse" 
                        data-bs-target="#collapse{{ $index }}">
                    {{ $item->name }}
                </button>
            </h2>
            <div id="collapse{{ $index }}" class="accordion-collapse collapse" 
                 data-bs-parent="#accordionExample">
                <div class="accordion-body">
                    <!-- Detailed content here -->
                </div>
            </div>
        </div>
    @endforeach
</div>
```

### 7. Modal Dialog
```blade
<!-- Trigger Button -->
<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
    Open Modal
</button>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modal Title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Content -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>
```

### 8. Tabs
```blade
<ul class="nav nav-tabs" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#tab1">Tab 1</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#tab2">Tab 2</a>
    </li>
</ul>
<div class="tab-content mt-3">
    <div class="tab-pane fade show active" id="tab1">
        Content 1
    </div>
    <div class="tab-pane fade" id="tab2">
        Content 2
    </div>
</div>
```

### 9. Alerts
```blade
<!-- Info Alert -->
<div class="alert alert-info" role="alert">
    <i class="fas fa-info-circle me-2"></i>
    <strong>Info:</strong> Information message
</div>

<!-- Success Alert -->
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i>
    <strong>Success!</strong> Operation completed
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>

<!-- Warning Alert -->
<div class="alert alert-warning" role="alert">
    <i class="fas fa-exclamation-triangle me-2"></i>
    <strong>Warning:</strong> Please note
</div>

<!-- Danger Alert -->
<div class="alert alert-danger" role="alert">
    <i class="fas fa-times-circle me-2"></i>
    <strong>Error:</strong> Something went wrong
</div>
```

### 10. Badges
```blade
<!-- Status badges -->
<span class="badge bg-success">Active</span>
<span class="badge bg-warning text-dark">Pending</span>
<span class="badge bg-danger">Inactive</span>
<span class="badge bg-info">Draft</span>
<span class="badge bg-primary">New</span>

<!-- Hierarchy level badges -->
<span class="badge bg-primary">L1</span>
<span class="badge bg-success">L2</span>
<span class="badge bg-warning text-dark">L3</span>
<span class="badge bg-info">L4</span>
```

## Color Scheme Standard

### Text Colors
- `text-primary` - Blue (primary actions, main categories)
- `text-success` - Green (success, active status, sub1)
- `text-warning` - Yellow/Orange (warnings, sub2)
- `text-info` - Cyan (info, detail level)
- `text-danger` - Red (errors, delete actions)
- `text-muted` - Gray (secondary text, descriptions)
- `text-dark` - Dark gray/black (main text)
- `text-white` - White (text on colored backgrounds)

### Background Colors
- `bg-primary` - Blue backgrounds
- `bg-success` - Green backgrounds
- `bg-warning` - Yellow backgrounds
- `bg-info` - Cyan backgrounds
- `bg-danger` - Red backgrounds
- `bg-light` - Light gray (card headers, footers)
- `bg-white` - White backgrounds
- `bg-dark` - Dark backgrounds

### Border Colors
- `border-primary` - Blue borders for L1/Main cards
- `border-success` - Green borders for L2/Sub1 cards
- `border-warning` - Yellow borders for L3/Sub2 cards
- `border-info` - Cyan borders for L4/Detail cards

## Button Styles

### Standard Buttons
```blade
<!-- Primary action -->
<button class="btn btn-primary">Primary Action</button>

<!-- Secondary action -->
<button class="btn btn-secondary">Cancel</button>

<!-- Success action -->
<button class="btn btn-success">Save</button>

<!-- Danger action -->
<button class="btn btn-danger">Delete</button>

<!-- Warning action -->
<button class="btn btn-warning">Warning</button>

<!-- Info action -->
<button class="btn btn-info">Info</button>
```

### Outline Buttons (for tables/compact spaces)
```blade
<button class="btn btn-sm btn-outline-primary">Edit</button>
<button class="btn btn-sm btn-outline-danger">Delete</button>
<button class="btn btn-sm btn-outline-success">Approve</button>
```

### Icon Buttons
```blade
<button class="btn btn-primary">
    <i class="fas fa-plus me-2"></i>Add New
</button>

<button class="btn btn-sm btn-outline-primary">
    <i class="fas fa-edit"></i>
</button>
```

## Spacing Utilities

### Margin
- `m-0` to `m-5` - All sides
- `mt-*`, `mb-*`, `ms-*`, `me-*` - Top, Bottom, Start, End
- `mx-*`, `my-*` - Horizontal, Vertical

### Padding
- `p-0` to `p-5` - All sides
- `pt-*`, `pb-*`, `ps-*`, `pe-*` - Top, Bottom, Start, End
- `px-*`, `py-*` - Horizontal, Vertical

### Gap (for flexbox/grid)
- `g-0` to `g-5` - Gap between grid columns/rows
- `gap-*` - Gap in flexbox

## Typography

### Headings
```blade
<h1 class="h1">Heading 1</h1> <!-- or use sizing -->
<h2 class="h2">Heading 2</h2>
<h2 class="h3">Heading 2 styled as h3</h2>
```

### Font Weight
- `fw-light` - Light (300)
- `fw-normal` - Normal (400)
- `fw-semibold` - Semibold (600)
- `fw-bold` - Bold (700)

### Font Size
- `fs-1` to `fs-6` - Font sizes
- `small` - Small text

### Text Alignment
- `text-start`, `text-center`, `text-end`
- `text-md-start` - Responsive alignment

## Icons Integration

### Font Awesome (Current)
```blade
<i class="fas fa-icon-name me-2"></i>Text
```

Common icons used:
- `fa-dashboard` - Dashboard
- `fa-table` - Tables
- `fa-filter` - Filters
- `fa-plus` - Add
- `fa-edit` - Edit
- `fa-trash` - Delete
- `fa-list` - List
- `fa-folder-tree` - Hierarchy
- `fa-layer-group` - Layers
- `fa-sitemap` - Structure
- `fa-info-circle` - Info
- `fa-check-circle` - Success
- `fa-exclamation-triangle` - Warning
- `fa-times-circle` - Error

## Responsive Design

### Breakpoints
- `xs` - <576px (default, no prefix)
- `sm` - ≥576px
- `md` - ≥768px
- `lg` - ≥992px
- `xl` - ≥1200px
- `xxl` - ≥1400px

### Responsive Columns
```blade
<!-- 12 cols on mobile, 6 on tablet, 3 on desktop -->
<div class="col-12 col-md-6 col-lg-3">
    <!-- Content -->
</div>
```

### Display Utilities
```blade
<!-- Hide on mobile, show on desktop -->
<div class="d-none d-md-block">Desktop only</div>

<!-- Show on mobile, hide on desktop -->
<div class="d-block d-md-none">Mobile only</div>
```

## Forms Standard

### Form Group
```blade
<div class="mb-3">
    <label for="inputId" class="form-label">Label</label>
    <input type="text" class="form-control" id="inputId" placeholder="Placeholder">
    <div class="form-text">Help text</div>
</div>
```

### Select Dropdown
```blade
<div class="mb-3">
    <label class="form-label">Select Option</label>
    <select class="form-select">
        <option selected>Choose...</option>
        <option value="1">Option 1</option>
    </select>
</div>
```

### Checkbox & Radio
```blade
<div class="form-check">
    <input class="form-check-input" type="checkbox" id="check1">
    <label class="form-check-label" for="check1">Check option</label>
</div>

<div class="form-check">
    <input class="form-check-input" type="radio" name="radio" id="radio1">
    <label class="form-check-label" for="radio1">Radio option</label>
</div>
```

## Livewire Integration

### Wire Directives with Bootstrap
```blade
<!-- Live search -->
<input type="text" wire:model.live.debounce.300ms="search" class="form-control">

<!-- Select filter -->
<select wire:model.live="filter" class="form-select">
    <option value="">All</option>
</select>

<!-- Sortable table header -->
<th wire:click="sortBy('field')" style="cursor: pointer;">
    Column Name
    @if($sortBy === 'field')
        <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
    @endif
</th>

<!-- Pagination -->
{{ $items->links('pagination::bootstrap-5') }}
```

## JavaScript Requirements

### Required in layout
```blade
<!-- Bootstrap Bundle (includes Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Alpine.js (for sidebar collapse) -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
```

## Migration Checklist

When converting from Tailwind to Bootstrap:

### Layout
- [ ] `flex` → `d-flex`
- [ ] `justify-between` → `justify-content-between`
- [ ] `items-center` → `align-items-center`
- [ ] `grid grid-cols-*` → `row` with `col-*`
- [ ] `gap-*` → `g-*` or `gap-*`

### Spacing
- [ ] `p-4` → `p-3` or `p-4` (values different)
- [ ] `px-*` → `px-*`
- [ ] `py-*` → `py-*`
- [ ] `m-*` → `m-*`
- [ ] `mb-6` → `mb-4` (max is 5 in Bootstrap)

### Colors
- [ ] `text-gray-700` → `text-dark` or `text-muted`
- [ ] `bg-blue-500` → `bg-primary`
- [ ] `bg-green-500` → `bg-success`
- [ ] `border-gray-200` → `border`

### Typography
- [ ] `text-2xl` → `h3` or `fs-3`
- [ ] `font-semibold` → `fw-semibold`
- [ ] `font-bold` → `fw-bold`
- [ ] `text-sm` → `small`

### Components
- [ ] Custom divs → `card`, `card-header`, `card-body`
- [ ] Table wrappers → `table-responsive`
- [ ] `min-w-full` → `w-100`
- [ ] `rounded-lg` → `rounded`
- [ ] `shadow-sm` → `shadow-sm`

## Best Practices

1. **Always use card components** for grouped content
2. **Use utility classes** instead of custom CSS when possible
3. **Keep spacing consistent** (use mb-4 for section spacing)
4. **Use semantic colors** (primary=main, success=active, danger=delete)
5. **Mobile-first approach** (design for mobile, enhance for desktop)
6. **Test responsiveness** at all breakpoints
7. **Use proper ARIA labels** for accessibility
8. **Consistent icon placement** (me-2 for icon before text)
9. **Table footers for pagination** (use card-footer)
10. **Empty states with icons** (fa-inbox with message)

## Common Patterns

### Dashboard Page
1. Header with title + actions
2. Statistics cards row (4 cards: primary, success, warning, info)
3. Filters card
4. Data table card with pagination
5. Info alert at bottom

### Form Page
1. Header with title
2. Card with form-body
3. Form groups with labels
4. Action buttons in card-footer

### List/Table Page
1. Header with search + filters
2. Statistics cards (optional)
3. Table card with hover effect
4. Pagination in footer

## Files Already Converted to Bootstrap

✅ `resources/views/livewire/dashboard.blade.php`
✅ `resources/views/livewire/balance-sheet-report.blade.php`
✅ `resources/views/livewire/coa-legacy.blade.php`
✅ `resources/views/layouts/admin.blade.php` (sidebar menu)

## Files Pending Conversion

Check these Livewire components:
- `resources/views/livewire/*.blade.php` (scan for Tailwind classes)

## References

- [Bootstrap 5 Documentation](https://getbootstrap.com/docs/5.3/)
- [Bootstrap 5 Components](https://getbootstrap.com/docs/5.3/components/)
- [Bootstrap 5 Utilities](https://getbootstrap.com/docs/5.3/utilities/)
- [Font Awesome Icons](https://fontawesome.com/icons)

## Support

For questions or issues with Bootstrap implementation, refer to:
1. This document first
2. Bootstrap official documentation
3. Project examples: dashboard.blade.php, coa-legacy.blade.php
