# COMPLETE FILE LIST FOR CPANEL UPLOAD

## Quick Reference: What to Upload

### ✅ NEW FILES (3 files)
1. `admin/ajax/upload-images.php`
2. `includes/upload-config.php`
3. `.htaccess`

### ✅ MODIFIED FILES (2 files)
1. `admin/property-edit-new.php`
2. `admin/blog-edit.php`

### 📄 DOCUMENTATION (3 files - optional)
1. `UPLOAD_FIX_DEPLOYMENT.md`
2. `FILES_MODIFIED.md`
3. `USER_EXPERIENCE_GUIDE.md`

---

## Detailed Upload Instructions

### NEW FILES

#### 1. admin/ajax/upload-images.php
```
Location: /admin/ajax/upload-images.php
Purpose: AJAX endpoint for image uploads
Action: CREATE NEW FILE
```

#### 2. includes/upload-config.php
```
Location: /includes/upload-config.php
Purpose: PHP configuration for large uploads
Action: CREATE NEW FILE
```

#### 3. .htaccess
```
Location: /.htaccess (root directory)
Purpose: Server-level upload configuration
Action: CREATE NEW FILE (or MERGE if exists)
⚠️ WARNING: If you already have .htaccess, ADD the contents to it, don't replace!
```

---

### MODIFIED FILES

#### 1. admin/property-edit-new.php
```
Location: /admin/property-edit-new.php
Purpose: Property/Development edit form
Action: REPLACE EXISTING FILE
⚠️ BACKUP FIRST: Save as property-edit-new.php.backup
```

#### 2. admin/blog-edit.php
```
Location: /admin/blog-edit.php
Purpose: Blog post edit form
Action: REPLACE EXISTING FILE
⚠️ BACKUP FIRST: Save as blog-edit.php.backup
```

---

## Upload Methods

### Method 1: cPanel File Manager (Recommended)
1. Log into cPanel
2. Open "File Manager"
3. Navigate to appropriate directory
4. Click "Upload"
5. Select file(s)
6. Wait for upload to complete

### Method 2: FTP Client (FileZilla, etc.)
1. Connect to your server via FTP
2. Navigate to appropriate directory
3. Drag and drop files
4. Ensure transfer mode is "Binary" or "Auto"

### Method 3: SSH/Terminal (Advanced)
```bash
# Upload via SCP
scp local-file.php user@server:/path/to/destination/
```

---

## Post-Upload Checklist

- [ ] All 5 files uploaded successfully
- [ ] File permissions set correctly (755 for folders, 644 for files)
- [ ] `/images/` folder has write permissions (755 or 775)
- [ ] PHP settings updated in cPanel (see UPLOAD_FIX_DEPLOYMENT.md)
- [ ] Tested with 30+ images on Property form
- [ ] Tested with 30+ images on Blog form
- [ ] Verified progress bar appears and works
- [ ] Confirmed images save correctly
- [ ] Checked that existing data is preserved

---

## File Permissions Guide

### Folders
```
/admin/           → 755
/admin/ajax/      → 755
/includes/        → 755
/images/          → 755 or 775 (needs write access)
```

### Files
```
All .php files    → 644
.htaccess         → 644
```

### How to Set Permissions in cPanel
1. Right-click file/folder
2. Select "Change Permissions"
3. Enter numeric value (e.g., 755)
4. Click "Change Permissions"

---

## Verification Steps

### 1. Check Files Exist
Navigate to each location and verify file exists:
- `/admin/ajax/upload-images.php` ✓
- `/includes/upload-config.php` ✓
- `/.htaccess` ✓
- `/admin/property-edit-new.php` ✓
- `/admin/blog-edit.php` ✓

### 2. Test Upload Endpoint
Visit: `https://yourdomain.com/admin/ajax/upload-images.php`
Expected: Blank page or JSON error (means file is accessible)
Error 404: File not uploaded correctly

### 3. Test Form
1. Go to Admin Panel
2. Click "Add Property"
3. Scroll to "Other Gallery Images"
4. Select 10-20 images
5. Watch for progress bar
6. Verify upload completes

---

## Common Upload Issues

### Issue: "File already exists"
**Solution**: 
- For NEW files: This shouldn't happen, check path
- For MODIFIED files: Choose "Overwrite" or delete old file first

### Issue: "Permission denied"
**Solution**:
- Check folder permissions (should be 755)
- Ensure you have write access to directory
- Contact hosting support if needed

### Issue: Upload times out
**Solution**:
- Upload files one at a time
- Use FTP instead of File Manager
- Check internet connection

### Issue: .htaccess causes 500 error
**Solution**:
- Delete .htaccess
- Use cPanel PHP settings instead
- Contact hosting support

---

## Rollback Plan

If something goes wrong:

1. **Restore backups**:
   - Rename `property-edit-new.php.backup` to `property-edit-new.php`
   - Rename `blog-edit.php.backup` to `blog-edit.php`

2. **Delete new files**:
   - Delete `admin/ajax/upload-images.php`
   - Delete `includes/upload-config.php`
   - Rename `.htaccess` to `.htaccess.disabled`

3. **Clear browser cache**:
   - Press Ctrl+Shift+Delete
   - Clear cache and cookies
   - Refresh page

---

## Support

If you need help:
1. Check `UPLOAD_FIX_DEPLOYMENT.md` for troubleshooting
2. Check server error logs in cPanel
3. Check browser console (F12) for JavaScript errors
4. Contact your hosting provider
5. Provide error messages and screenshots

---

**Last Updated**: January 2026
**Version**: 1.0
