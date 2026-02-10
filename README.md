# E-Commerce Website - Complete PHP + MySQL Solution

A complete, professional e-commerce website with admin panel, similar to saya.pk.

## Features

### Frontend
- ✅ Beautiful and modern UI design
- ✅ Product catalog with categories
- ✅ Product search functionality
- ✅ Shopping cart system
- ✅ User authentication (Login/Register)
- ✅ Checkout process
- ✅ Order management
- ✅ Responsive design

### Admin Panel
- ✅ Dashboard with statistics
- ✅ Product management (Create, Read, Update, Delete)
- ✅ Order management
- ✅ Order status updates
- ✅ User management

## Installation

1. **Database Setup**
   - Import `database.sql` file into your MySQL database
   - Update database credentials in `config/database.php`

2. **Configuration**
   - Open `config/database.php` and update:
     ```php
     'host' => 'localhost',
     'dbname' => 'ecommerce_db',
     'username' => 'root',
     'password' => '', // Your MySQL password
     ```

3. **Default Admin Login**
   - Email: `admin@example.com`
   - Password: `admin123`

4. **File Structure**
   ```
   haris web/
   ├── assets/
   │   ├── css/
   │   │   ├── style.css
   │   │   └── admin.css
   │   └── js/
   │       ├── main.js
   │       └── admin.js
   ├── config/
   │   └── database.php
   ├── core/
   │   ├── Router.php
   │   ├── Database.php
   │   ├── Session.php
   │   ├── Auth.php
   │   └── Helper.php
   ├── controllers/
   │   ├── HomeController.php
   │   ├── ProductController.php
   │   ├── CartController.php
   │   ├── CheckoutController.php
   │   ├── AuthController.php
   │   ├── OrderController.php
   │   └── admin/
   │       ├── DashboardController.php
   │       ├── ProductController.php
   │       └── OrderController.php
   ├── models/
   │   ├── Product.php
   │   ├── Category.php
   │   ├── Cart.php
   │   ├── Order.php
   │   └── User.php
   ├── views/
   │   ├── layouts/
   │   ├── admin/
   │   └── ...
   ├── database.sql
   ├── index.php
   └── .htaccess
   ```

## Usage

1. **Access the Website**
   - Open browser and navigate to: `http://localhost/haris web/`

2. **Admin Panel**
   - Login with admin credentials
   - Access admin panel at: `http://localhost/haris web/admin/dashboard`

3. **Add Products**
   - Go to Admin Panel > Products > Add New Product
   - Fill in product details
   - Note: For images, you can use image URLs or upload images to `assets/images/` folder

## Technologies Used

- PHP 7.4+
- MySQL 5.7+
- HTML5, CSS3, JavaScript
- Font Awesome Icons
- Modern CSS Grid & Flexbox

## Features in Detail

### Product Management
- Create, edit, delete products
- Set prices, sale prices, stock
- Category assignment
- Featured products
- Product images

### Order Management
- View all orders
- Update order status
- View order details
- Track order items

### Shopping Cart
- Add/remove items
- Update quantities
- Session-based cart (works without login)
- User-specific cart (after login)

### User System
- Registration
- Login/Logout
- Order history
- Profile management

## Security Features

- Password hashing (bcrypt)
- SQL injection prevention (PDO prepared statements)
- Session management
- Admin authentication middleware
- CSRF protection ready

## Customization

- **Colors**: Edit CSS variables in `assets/css/style.css`
- **Logo**: Update in `views/layouts/header.php`
- **Database**: Modify `config/database.php`
- **Routes**: Add new routes in `index.php`

## Notes

- Make sure PHP PDO extension is enabled
- Ensure mod_rewrite is enabled for Apache
- Create `assets/images/` folder for product images
- Default admin password should be changed in production

## Support

For issues or questions, check the code comments or database structure in `database.sql`.

Frontend: http://localhost/haris web/
Admin: http://localhost/haris web/admin/dashboard

http://localhost/haris web/check_admin.php