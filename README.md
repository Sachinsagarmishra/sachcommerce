# TrendsOne - Complete eCommerce Website

A full-featured eCommerce platform built with PHP, MySQL, Bootstrap 5, Razorpay payment gateway, and PHPMailer. Optimized for SEO (India market) with complete responsive design.

## 🚀 Features

### Frontend Features
- ✅ Responsive design (Mobile, Tablet, Desktop)
- ✅ Product browsing with filters and sorting
- ✅ Product search functionality
- ✅ Shopping cart (guest & logged-in users)
- ✅ Wishlist functionality
- ✅ User authentication (Login, Register, Password Reset)
- ✅ User dashboard (Orders, Profile, Addresses)
- ✅ Checkout with multiple payment options
- ✅ Razorpay payment integration
- ✅ Cash on Delivery (COD)
- ✅ Order tracking
- ✅ Product reviews and ratings
- ✅ Blog system
- ✅ Newsletter subscription
- ✅ Contact form
- ✅ SEO optimized pages

### Admin Panel Features
- ✅ Secure admin authentication
- ✅ Dashboard with statistics and charts
- ✅ Product management (CRUD operations)
- ✅ Bulk product upload (CSV)
- ✅ Category management
- ✅ Order management with status updates
- ✅ Customer management
- ✅ Coupon management
- ✅ Review moderation
- ✅ Blog post management
- ✅ Sales reports and analytics
- ✅ Email settings (SMTP configuration)
- ✅ Payment settings (Razorpay)
- ✅ General site settings
- ✅ SEO settings

### Technical Features
- ✅ PDO database connection (prepared statements)
- ✅ CSRF protection
- ✅ XSS protection
- ✅ SQL injection prevention
- ✅ Password hashing (bcrypt)
- ✅ Session management
- ✅ Email notifications (PHPMailer)
- ✅ Image upload and validation
- ✅ SEO-friendly URLs (.htaccess)
- ✅ Schema.org markup
- ✅ Sitemap and robots.txt
- ✅ Browser caching
- ✅ Gzip compression

## 📋 Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache web server with mod_rewrite enabled
- XAMPP/WAMP/LAMP (for local development)

## 🛠️ Installation

### Step 1: Extract Files
Extract all files to your web server directory:
- XAMPP: `C:\xampp\htdocs\trendsone\`
- WAMP: `C:\wamp\www\trendsone\`
- Linux: `/var/www/html/trendsone/`

### Step 2: Create Database
1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Create a new database named `trendsone_db`
3. Import the SQL file: `database/schema.sql`

### Step 3: Configure Settings
Edit `config/config.php` and update:

```php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'trendsone_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Site URL
define('SITE_URL', 'http://localhost/trendsone');

// Razorpay Keys (Get from https://razorpay.com)
define('RAZORPAY_TEST_KEY_ID', 'your_test_key_id');
define('RAZORPAY_TEST_KEY_SECRET', 'your_test_key_secret');

// SMTP Settings (Gmail example)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password');
```

### Step 4: Set Permissions
Set write permissions for upload directories:
```bash
chmod -R 755 uploads/
```

### Step 5: Access the Website

**Frontend:** http://localhost/trendsone/

**Admin Panel:** http://localhost/trendsone/admin/

**Default Admin Credentials:**
- Email: admin@trendsone.com
- Password: admin123

⚠️ **IMPORTANT:** Change admin password after first login!

## 📁 Project Structure

```
trendsone/
├── admin/                      # Admin panel
│   ├── assets/                 # Admin CSS, JS, images
│   ├── includes/               # Admin includes (header, footer, sidebar)
│   ├── index.php               # Admin login
│   ├── dashboard.php           # Admin dashboard
│   ├── products.php            # Product management
│   ├── orders.php              # Order management
│   └── ...                     # Other admin pages
├── api/                        # API endpoints
│   ├── add-to-cart.php
│   ├── razorpay-payment.php
│   └── ...
├── assets/                     # Frontend assets
│   ├── css/                    # Stylesheets
│   ├── js/                     # JavaScript files
│   └── images/                 # Images
├── config/                     # Configuration
│   └── config.php              # Main config file
├── database/                   # Database files
│   └── schema.sql              # Database schema
├── email-templates/            # Email templates
├── includes/                   # Frontend includes
│   ├── header.php
│   ├── footer.php
│   ├── navbar.php
│   └── functions.php
├── uploads/                    # User uploads
│   ├── products/
│   ├── categories/
│   ├── blog/
│   └── avatars/
├── vendor/                     # Third-party libraries
│   ├── razorpay/
│   └── phpmailer/
├── index.php                   # Homepage
├── shop.php                    # Product listing
├── product-detail.php          # Product details
├── cart.php                    # Shopping cart
├── checkout.php                # Checkout page
├── .htaccess                   # Apache configuration
├── robots.txt                  # SEO robots file
└── README.md                   # This file
```

## 🔧 Configuration

### Razorpay Setup
1. Sign up at https://razorpay.com
2. Get your API keys from Dashboard
3. Update keys in `config/config.php`
4. Test with test mode before going live

### Email Setup (Gmail)
1. Enable 2-factor authentication on Gmail
2. Generate App Password: https://myaccount.google.com/apppasswords
3. Update SMTP settings in `config/config.php`

### SEO Configuration
1. Update meta tags in `config/config.php`
2. Generate sitemap: Access `/sitemap.xml`
3. Submit to Google Search Console
4. Add Google Analytics ID in settings

## 🎨 Customization

### Change Logo
Replace files in `assets/images/`:
- `logo.png` - Main logo
- `favicon.ico` - Browser favicon

### Modify Colors
Edit `assets/css/style.css` and update CSS variables:
```css
:root {
    --primary-color: #4e73df;
    --secondary-color: #858796;
}
```

### Add Payment Methods
Edit `checkout.php` to add more payment gateways

## 📧 Email Templates

Email templates are located in `email-templates/`:
- `order-confirmation.php` - Order placed
- `order-status-update.php` - Status changed
- `registration-welcome.php` - New user
- `password-reset.php` - Password reset

## 🔒 Security Features

- ✅ Prepared statements (SQL injection prevention)
- ✅ CSRF tokens on all forms
- ✅ XSS protection (htmlspecialchars)
- ✅ Password hashing (bcrypt, cost 12)
- ✅ Secure session handling
- ✅ File upload validation
- ✅ Admin role verification
- ✅ HTTPS enforcement (production)

## 📱 Responsive Design

Tested on:
- ✅ Desktop (1920x1080, 1366x768)
- ✅ Tablet (iPad, Android tablets)
- ✅ Mobile (iPhone, Android phones)
- ✅ Browsers: Chrome, Firefox, Safari, Edge

## 🐛 Troubleshooting

### Database Connection Error
- Check database credentials in `config/config.php`
- Ensure MySQL service is running
- Verify database exists

### Images Not Displaying
- Check file permissions on `uploads/` folder
- Verify SITE_URL in config matches your domain

### Email Not Sending
- Verify SMTP credentials
- Check if port 587 is open
- Enable "Less secure apps" for Gmail (or use App Password)

### Razorpay Payment Failing
- Ensure you're using correct API keys
- Check if test/live mode matches environment
- Verify webhook URL is configured

### .htaccess Not Working
- Enable mod_rewrite in Apache
- Check AllowOverride is set to All
- Restart Apache after changes

## 📊 Sample Data

The database includes sample data:
- 1 Admin user
- 5 Categories
- Sample products (add your own)

## 🚀 Deployment to Production

1. **Update config.php:**
   - Set ENVIRONMENT to 'production'
   - Update SITE_URL to your domain
   - Use live Razorpay keys

2. **Enable HTTPS:**
   - Uncomment HTTPS redirect in `.htaccess`

3. **Security:**
   - Change admin password
   - Update database credentials
   - Set strong passwords

4. **Optimize:**
   - Enable caching
   - Minify CSS/JS
   - Optimize images

5. **SEO:**
   - Submit sitemap to Google
   - Set up Google Analytics
   - Configure meta tags

## 📝 Testing Checklist

- [ ] User registration and login
- [ ] Email verification
- [ ] Password reset
- [ ] Product browsing and search
- [ ] Add to cart (guest & logged-in)
- [ ] Checkout process
- [ ] Razorpay payment (test mode)
- [ ] COD orders
- [ ] Order confirmation emails
- [ ] Admin login
- [ ] Product CRUD operations
- [ ] Order management
- [ ] Review moderation
- [ ] Blog posts
- [ ] Responsive design testing

## 🔄 Updates & Maintenance

### Regular Tasks
- Backup database weekly
- Monitor error logs
- Update product inventory
- Review and approve customer reviews
- Process pending orders
- Check low stock alerts

### Security Updates
- Keep PHP updated
- Update dependencies regularly
- Monitor for security vulnerabilities
- Review access logs

## 📞 Support

For issues or questions:
- Check documentation above
- Review code comments
- Test with sample data first

## 📄 License

This project is provided as-is for educational and commercial use.

## 🙏 Credits

Built with:
- PHP & MySQL
- Bootstrap 5
- jQuery
- Font Awesome
- Chart.js
- DataTables
- Select2
- SweetAlert2
- Razorpay SDK
- PHPMailer

---

**Version:** 1.0.0  
**Last Updated:** November 2024  
**Author:** TrendsOne Development Team

🎉 **Thank you for using TrendsOne eCommerce Platform!**
