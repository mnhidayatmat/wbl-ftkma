# Assets Storage Guide

## 📁 Directory Structure

In Laravel, static assets (images, CSS, JS) are stored in the `public` directory:

```
public/
├── images/
│   ├── logos/          # University logos, company logos
│   └── uploads/        # User-uploaded images
├── css/                # Additional CSS files (if needed)
├── js/                 # Additional JS files (if needed)
└── index.php           # Laravel entry point
```

## 🖼️ How to Use Images in Blade Templates

### Method 1: Using `asset()` helper (Recommended)

```blade
<!-- Logo image -->
<img src="{{ asset('images/logos/umpsa-logo.png') }}" alt="UMPSA Logo">

<!-- User uploaded image -->
<img src="{{ asset('images/uploads/profile.jpg') }}" alt="Profile Picture">
```

### Method 2: Using `url()` helper

```blade
<img src="{{ url('images/logos/umpsa-logo.png') }}" alt="UMPSA Logo">
```

### Method 3: Direct path (Not recommended, but works)

```blade
<img src="/images/logos/umpsa-logo.png" alt="UMPSA Logo">
```

## 📝 Example: Adding University Logo to Login Page

1. **Place your logo file:**
   ```
   public/images/logos/umpsa-logo.png
   ```

2. **Update the Blade template:**
   ```blade
   <!-- Replace the placeholder logo with: -->
   <img src="{{ asset('images/logos/umpsa-logo.png') }}" 
        alt="UMPSA Logo" 
        class="w-48 h-56 object-contain">
   ```

## 🔒 User-Uploaded Files

For user-uploaded files (like profile pictures), you have two options:

### Option 1: Store in `public/images/uploads/`
- **Pros:** Directly accessible via URL
- **Cons:** Less secure, files are publicly accessible

### Option 2: Store in `storage/app/public/` (Recommended for production)
- **Pros:** More secure, can control access
- **Cons:** Requires symlink: `php artisan storage:link`

**To use storage:**
```bash
php artisan storage:link
```

Then access files via:
```blade
{{ asset('storage/uploads/profile.jpg') }}
```

## 📦 Best Practices

1. **Organize by type:**
   - `public/images/logos/` - Logos and branding
   - `public/images/icons/` - Icons
   - `public/images/banners/` - Banner images
   - `public/images/uploads/` - User uploads

2. **Use descriptive filenames:**
   - ✅ `umpsa-logo.png`
   - ❌ `img1.png`

3. **Optimize images:**
   - Compress images before uploading
   - Use appropriate formats (PNG for logos, JPG for photos)
   - Consider WebP for better performance

4. **Always use `asset()` helper:**
   - Works with subdirectories
   - Handles URL generation correctly
   - Works with CDN if configured

## 🎨 Example: Complete Image Usage

```blade
<!-- In your Blade template -->
<div class="flex items-center">
    <img src="{{ asset('images/logos/umpsa-logo.png') }}" 
         alt="UMPSA Logo"
         class="w-32 h-32 object-contain">
    <h1>WBL System</h1>
</div>

<!-- With error handling -->
<img src="{{ asset('images/logos/umpsa-logo.png') }}" 
     alt="UMPSA Logo"
     onerror="this.src='{{ asset('images/logos/default-logo.png') }}'"
     class="w-48 h-56">
```

## 🔗 Quick Reference

- **Static images:** `public/images/`
- **User uploads:** `storage/app/public/` (with symlink)
- **CSS files:** `public/css/` or `resources/css/` (compiled)
- **JS files:** `public/js/` or `resources/js/` (compiled)

