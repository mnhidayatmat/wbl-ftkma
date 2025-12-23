# Students Page Redesign - Implementation Guide

## Overview
The Students page has been completely redesigned with modern UI components, filtering, search, sorting, and responsive design using UMPSA official color palette.

## Features Implemented

### 1. Horizontal Tab Component
- **Component**: `resources/views/components/tab-group.blade.php`
- **Features**:
  - Desktop: Horizontal tabs with active state highlighting
  - Mobile: Horizontal scrollable tabs
  - Dynamic filtering via query parameters
  - UMPSA color theme applied

### 2. Search Functionality
- Search by student name or matric number
- Real-time filtering with query parameters
- Clear button to reset search
- Preserves current filters (group, sorting)

### 3. Sortable Table Columns
- Sortable columns: Name, Matric No, Programme, Company
- Visual indicators (arrows) for sort direction
- Click column headers to sort
- Maintains current filters when sorting

### 4. Group Badges
- **Component**: `resources/views/components/badge.blade.php`
- Color-coded badges using UMPSA palette:
  - Group 1: Primary (#003A6C)
  - Group 2: Secondary (#0084C5)
  - Group 3: Accent (#00AEEF)
  - Group 4: Primary with opacity
  - Group 5: Secondary with opacity

### 5. Responsive Design
- **Desktop**: Full table with all columns
- **Mobile**: Card-based layout with stacked information
- Tabs convert to horizontal scroll on mobile
- Touch-friendly buttons and spacing

## Files Created/Modified

### New Components
1. **`resources/views/components/tab-group.blade.php`**
   - Reusable tab navigation component
   - Supports active state and query parameters

2. **`resources/views/components/badge.blade.php`**
   - Reusable badge component
   - Multiple variants and sizes

3. **`resources/views/components/table.blade.php`**
   - Reusable table component
   - Supports sortable columns with icons

### Modified Files
1. **`app/Http/Controllers/StudentController.php`**
   - Added filtering by group
   - Added search functionality
   - Added sorting functionality
   - Query parameter handling

2. **`resources/views/students/index.blade.php`**
   - Complete redesign with tabs, search, and improved table
   - Mobile-responsive card layout
   - Group badges integration

3. **`tailwind.config.js`**
   - Added new UMPSA colors:
     - `umpsa-primary`: #003A6C
     - `umpsa-secondary`: #0084C5
     - `umpsa-accent`: #00AEEF

4. **`resources/css/app.css`**
   - Added utility classes for new UMPSA colors

## Usage Examples

### Tab Component
```blade
<x-tab-group 
    :tabs="$tabs" 
    :activeTab="$activeTab" 
    :baseUrl="route('students.index')"
/>
```

### Badge Component
```blade
<x-badge variant="group-1" size="sm">
    Group 1
</x-badge>
```

### Table Component
```blade
<x-table :columns="$columns">
    <!-- Table rows here -->
</x-table>
```

## URL Structure

### Filter by Group
```
/students?group=1
/students?group=2
```

### Search
```
/students?search=john
/students?group=1&search=john
```

### Sorting
```
/students?sort_by=name&sort_dir=asc
/students?group=1&sort_by=matric_no&sort_dir=desc
```

### Combined
```
/students?group=1&search=john&sort_by=name&sort_dir=asc
```

## Color Palette

### UMPSA Official Colors
- **Primary**: #003A6C (Tabs, Badges)
- **Secondary**: #0084C5 (Tabs, Badges)
- **Accent**: #00AEEF (Tabs, Badges)

### Existing UMPSA Colors (Still Used)
- **Teal**: #009E9A (Card headers, links)
- **Deep Blue**: #003B73 (Headings, sidebar)
- **Royal Blue**: #005AA7 (Hover states)

## Responsive Breakpoints

- **Mobile**: < 768px (md breakpoint)
  - Horizontal scroll tabs
  - Card-based layout
  - Stacked information

- **Desktop**: ≥ 768px
  - Full-width tabs
  - Table layout
  - All columns visible

## Testing Checklist

- [ ] Tabs filter students correctly
- [ ] Search works with name and matric number
- [ ] Sorting works on all sortable columns
- [ ] Group badges display correctly
- [ ] Mobile layout displays as cards
- [ ] Pagination maintains filters
- [ ] Clear search button works
- [ ] All query parameters persist correctly

## Next Steps

1. Rebuild assets: `npm run dev`
2. Test all filtering combinations
3. Verify mobile responsiveness
4. Check dark mode compatibility
5. Test with different screen sizes

