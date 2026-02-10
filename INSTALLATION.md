# Installation Guide - E-Commerce Website

## Step-by-Step Installation

### 1. Database Setup

1. Open phpMyAdmin or MySQL command line
2. Create a new database (or use existing):
   ```sql
   CREATE DATABASE ecommerce_db;
   ```
3. Import the database structure:
   - Open phpMyAdmin
   - Select `ecommerce_db` database
   - Go to "Import" tab
   - Choose `database.sql` file
   - Click "Go"

### 2. Configure Database Connection

Edit `config/database.php`:
```php
return [
    'host' => 'localhost',
    'dbname' => 'ecommerce_db',
    'username' => 'root',        // Your MySQL username
    'password' => '',            // Your MySQL password
    'charset' => 'utf8mb4'
];
```

### 3. File Permissions

Make sure these folders are writable (if needed for file uploads):
- `assets/images/` (for product images)

### 4. Web Server Configuration

**For XAMPP (Windows):**
- Place project in `C:\xampp\htdocs\haris web\`
- Access via: `http://localhost/haris web/`

**For Apache:**
- Ensure `mod_rewrite` is enabled
- `.htaccess` file should be in root directory

**For Nginx:**
- Add rewrite rules to nginx config:
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

### 5. Default Login Credentials

**Admin Panel:**
- Email: `admin@example.com`
- Password: `admin123`

⚠️ **Important:** Change the admin password after first login!

### 6. Testing the Installation

1. Open browser: `http://localhost/haris web/`
2. You should see the homepage with featured products
3. Try adding products to cart
4. Login to admin panel: `http://localhost/haris web/admin/dashboard`

### 7. Adding Product Images

1. Upload product images to `assets/images/` folder
2. In admin panel, when creating/editing products:
   - Enter image filename (e.g., `product1.jpg`)
   - Or use full URL for external images

### 8. Troubleshooting

**Issue: 404 errors on routes**
- Check if `.htaccess` file exists
- Ensure `mod_rewrite` is enabled
- Verify base path in Router.php matches your setup

**Issue: Database connection error**
- Verify database credentials in `config/database.php`
- Check if MySQL service is running
- Ensure database `ecommerce_db` exists

**Issue: Images not showing**
- Check `assets/images/` folder exists
- Verify image filenames match database entries
- Check file permissions

**Issue: Session errors**
- Ensure PHP session directory is writable
- Check `php.ini` session settings

### 9. Production Checklist

Before going live:
- [ ] Change admin password
- [ ] Update database credentials
- [ ] Remove debug code
- [ ] Set proper file permissions
- [ ] Enable HTTPS
- [ ] Configure proper error logging
- [ ] Set up backup system
- [ ] Test all functionality

### 10. Features to Test

- [ ] User registration and login
- [ ] Browse products
- [ ] Search products
- [ ] Add to cart
- [ ] Checkout process
- [ ] Order placement
- [ ] Admin login
- [ ] Add/edit products
- [ ] Manage orders
- [ ] Update order status

## Support

If you encounter any issues:
1. Check PHP error logs
2. Verify all files are uploaded correctly
3. Check database connection
4. Ensure PHP version is 7.4 or higher
5. Verify MySQL version is 5.7 or higher

## Next Steps

After installation:
1. Add your products via admin panel
2. Customize colors and branding
3. Add product images
4. Configure payment gateway (if needed)
5. Set up email notifications (if needed)

