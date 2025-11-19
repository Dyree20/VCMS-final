# Filter & Icons - Quick Reference Guide

## 🎨 Icon Usage

### Libraries Available
```html
<!-- Font Awesome 6.5.0 (fa-solid, fa-regular, fa-light, fa-brands) -->
<i class="fa-solid fa-user"></i>
<i class="fa-solid fa-check"></i>
<i class="fa-solid fa-xmark"></i>

<!-- Boxicons (bx, bx-s, bxl) -->
<i class='bx bx-user'></i>
<i class='bx bx-home'></i>
<i class='bx bx-car'></i>
```

### Common Icons (Font Awesome 6.5.0)
| Icon Name | Code | Usage |
|-----------|------|-------|
| Users | `fa-users` | User count |
| User Check | `fa-user-check` | Active status |
| Hourglass Half | `fa-hourglass-half` | Pending status |
| User Slash | `fa-user-slash` | Inactive status |
| Pen | `fa-pen` | Edit action |
| Check | `fa-check` | Approve/confirm |
| Xmark | `fa-xmark` | Close/cancel |
| Trash | `fa-trash` | Delete |
| Arrow Right | `fa-arrow-right` | Next/forward |
| Chevron Right | `fa-chevron-right` | Navigation |

### ⚠️ Deprecated Icons (v5 → v6)
| Old (v5) | New (v6) |
|----------|----------|
| `fa-times` | `fa-xmark` |
| `fa-times-circle` | `fa-circle-xmark` |
| `fa-sign-out-alt` | `fa-right-from-bracket` |
| `fa-plus-circle` | `fa-circle-plus` |

---

## 🔍 Filter Implementation

### 1. Client-Side Filters (Instant, No Server Request)

#### User Page Example
```javascript
// Already implemented in public/js/user-filters.js
const searchInput = document.getElementById('searchInput');
const statusFilter = document.getElementById('statusFilter');
const roleFilter = document.getElementById('roleFilter');

searchInput.addEventListener('keyup', performFilter);
statusFilter.addEventListener('change', performFilter);
roleFilter.addEventListener('change', performFilter);
```

**Benefits**: No server load, instant results, works offline

#### To Add a New Client-Side Filter
1. Create filter script in `public/js/` folder
2. Add script tag to blade template
3. Get filter inputs by ID
4. Listen to change/keyup events
5. Show/hide table rows based on criteria

---

### 2. AJAX Filters (Server-Side, With Debouncing)

#### Front-Desk Search Example
```javascript
const performSearch = debounce(function() {
    const searchQuery = searchInput.value;
    const statusValue = statusFilter.value;
    
    fetch(`{{ route('front-desk.search') }}?q=${searchQuery}&status=${statusValue}`)
        .then(response => response.json())
        .then(data => {
            // Update table with results
            tbody.innerHTML = '';
            data.inquiries.forEach(inquiry => {
                tbody.innerHTML += `<tr>...</tr>`;
            });
        });
}, 300); // 300ms debounce

searchInput.addEventListener('keyup', performSearch);
statusFilter.addEventListener('change', performSearch);
```

**Benefits**: Flexible server-side logic, large datasets, advanced queries

---

## 📋 Filter by Page

### Users Management Page
**Location**: `resources/views/users.blade.php`
**Script**: `public/js/user-filters.js`
**Filters**:
- Search: name, username, email, phone
- Status: active, pending, inactive
- Role: admin, enforcer, user

```html
<input type="text" id="searchInput" placeholder="Search users...">
<select id="statusFilter">
    <option value="">All Status</option>
    <option value="active">Active</option>
    <option value="pending">Pending</option>
    <option value="inactive">Inactive</option>
</select>
<select id="roleFilter">
    <option value="">All Roles</option>
    <option value="admin">Admin</option>
    <option value="enforcer">Enforcer</option>
    <option value="user">User</option>
</select>
```

---

### Clamping Records Page
**Location**: `resources/views/clamping.blade.php`
**Script**: `public/js/clamping-filters.js`
**Filters**:
- Search: plate number, ticket number
- Status: pending, accepted, paid, approved, released, cancelled

```html
<input type="text" name="search" placeholder="Search by Plate / Ticket">
<select name="status">
    <option value="">All Status</option>
    <option value="pending">Pending</option>
    <option value="accepted">Accepted</option>
    <option value="paid">Paid</option>
</select>
```

---

### Payment Page
**Location**: `resources/views/payment.blade.php`
**Script**: `public/js/payment-filters.js`
**Filters**:
- Search: plate, ticket ID
- Status: all, paid, unpaid, pending

```html
<input type="text" placeholder="Search by Plate No. / Ticket ID">
<select>
    <option>All Status</option>
    <option>Paid</option>
    <option>Unpaid</option>
    <option>Pending</option>
</select>
```

---

## 🛠️ How to Add a New Filter

### Step 1: Create Filter Script
```javascript
// public/js/my-page-filters.js
document.addEventListener('DOMContentLoaded', () => {
    const table = document.querySelector('.custom-table');
    const searchInput = document.querySelector('input[name="search"]');
    const statusFilter = document.querySelector('select[name="status"]');
    
    const performFilter = () => {
        const searchQuery = (searchInput?.value || '').toLowerCase();
        const status = (statusFilter?.value || '').toLowerCase();
        
        table.querySelectorAll('tbody tr').forEach(row => {
            const matches = row.textContent.toLowerCase().includes(searchQuery);
            row.style.display = matches ? '' : 'none';
        });
    };
    
    searchInput?.addEventListener('keyup', performFilter);
    statusFilter?.addEventListener('change', performFilter);
});
```

### Step 2: Add to Blade Template
```html
@section('content')
    <!-- Your table here -->
@endsection

<script src="{{ asset('js/my-page-filters.js') }}"></script>
```

### Step 3: Test
- Type in search input
- Change filter select
- Verify rows show/hide

---

## 🧪 Testing Filters

### Manual Test Checklist
- [ ] Search by first criteria
- [ ] Search by second criteria
- [ ] Combine multiple filters
- [ ] Clear search (empty result handling)
- [ ] Verify no results message displays
- [ ] Check responsive on mobile
- [ ] Verify no console errors

### Console Check
```javascript
// Open browser DevTools (F12)
// Check Console tab for errors
// Type in search/filter to verify no errors
```

---

## 📂 File Structure

```
VCMSystem/
├── public/
│   └── js/
│       ├── user-filters.js          ← User page filters
│       ├── clamping-filters.js      ← Clamping page filters
│       ├── payment-filters.js       ← Payment page filters
│       └── ...other scripts
│
└── resources/
    └── views/
        ├── users.blade.php          ← Has: <script src=".../user-filters.js"></script>
        ├── clamping.blade.php       ← Has: <script src=".../clamping-filters.js"></script>
        ├── payment.blade.php        ← Has: <script src=".../payment-filters.js"></script>
        ├── layouts/
        │   ├── app.blade.php        ← Font Awesome + Boxicons
        │   └── front-desk.blade.php ← Font Awesome + Boxicons
        └── ...
```

---

## 🚀 Performance Tips

1. **Use Client-Side Filters For**:
   - Small datasets (< 1000 rows)
   - Frequently accessed pages
   - Instant feedback needed

2. **Use AJAX Filters For**:
   - Large datasets (> 1000 rows)
   - Complex database queries
   - Real-time updates needed

3. **Optimization**:
   - Always add debounce delay (300-500ms) for AJAX
   - Use event delegation for dynamic content
   - Minimize DOM manipulation

---

## 🆘 Troubleshooting

### Icons not showing?
```html
<!-- Check these are in layout head -->
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
```

### Filters not working?
1. Check console (F12) for errors
2. Verify script is loaded (`<script src="..."></script>`)
3. Verify element IDs match in HTML and JS
4. Check table structure matches filter logic

### AJAX returning empty?
1. Verify route exists in `routes/api.php` or `routes/web.php`
2. Check controller method returns JSON
3. Test route directly in browser: `/front-desk/search?q=test`

---

## 📞 Support

For questions about filters or icons, refer to:
- `FILTER_AND_ICONS_FIX_SUMMARY.md` (comprehensive documentation)
- Individual filter scripts in `public/js/`
- Blade templates with filter implementation
