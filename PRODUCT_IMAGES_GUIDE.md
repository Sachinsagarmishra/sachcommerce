# Product Images Upload Guide

## 📁 Image Path Structure

### Real Image Paths:
```
uploads/
└── products/
    ├── samsung-s23.jpg
    ├── iphone-15-pro.jpg
    ├── sony-headphones.jpg
    └── ... (your product images)
```

### Full Path:
```
C:\xampp\htdocs\trendsone\uploads\products\
```

### URL Path:
```
http://localhost/trendsone/uploads/products/samsung-s23.jpg
```

---

## 🎯 How Images Work:

### In Database:
```sql
primary_image = 'samsung-s23.jpg'
```

### In Code:
```php
PRODUCT_IMAGE_URL . $product['primary_image']
// Results in: http://localhost/trendsone/uploads/products/samsung-s23.jpg
```

### Fallback:
```php
// If primary_image is NULL or empty:
https://via.placeholder.com/300x250?text=No+Image
```

---

## 📤 How to Upload Product Images:

### Method 1: Manual Upload (Quick)

#### Step 1: Create Folder
```
1. Go to: C:\xampp\htdocs\trendsone\
2. Create folder: uploads
3. Inside uploads, create: products
4. Final path: C:\xampp\htdocs\trendsone\uploads\products\
```

#### Step 2: Add Images
```
1. Copy your product images to: C:\xampp\htdocs\trendsone\uploads\products\
2. Rename images to simple names (e.g., samsung-s23.jpg, iphone-15.jpg)
3. Supported formats: .jpg, .jpeg, .png, .webp
```

#### Step 3: Update Database
```sql
-- Option A: Update via phpMyAdmin
UPDATE products 
SET primary_image = 'samsung-s23.jpg' 
WHERE slug = 'samsung-galaxy-s23-ultra';

-- Option B: Update all at once
UPDATE products SET primary_image = 'samsung-s23.jpg' WHERE id = 1;
UPDATE products SET primary_image = 'iphone-15.jpg' WHERE id = 2;
UPDATE products SET primary_image = 'sony-headphones.jpg' WHERE id = 3;
-- ... and so on
```

---

### Method 2: Via Admin Panel (Recommended)

#### Step 1: Login to Admin
```
http://localhost/trendsone/admin/
Email: admin@trendsone.com
Password: admin123
```

#### Step 2: Edit Product
```
1. Go to: Products → All Products
2. Click "Edit" on any product
3. Upload image in "Product Images" section
4. Set as primary image
5. Click "Save"
```

---

## 📋 Quick Setup SQL:

### Create Upload Folder Structure:
```bash
# Run in Command Prompt (as Administrator)
cd C:\xampp\htdocs\trendsone
mkdir uploads
mkdir uploads\products
mkdir uploads\categories
mkdir uploads\blog
mkdir uploads\avatars
```

### Sample Image Names for Your Products:

```sql
-- Electronics
UPDATE products SET primary_image = 'samsung-s23-ultra.jpg' WHERE id = 1;
UPDATE products SET primary_image = 'iphone-15-pro.jpg' WHERE id = 2;
UPDATE products SET primary_image = 'sony-wh1000xm5.jpg' WHERE id = 3;
UPDATE products SET primary_image = 'dell-xps-15.jpg' WHERE id = 4;
UPDATE products SET primary_image = 'apple-watch-9.jpg' WHERE id = 5;

-- Fashion
UPDATE products SET primary_image = 'levis-511-jeans.jpg' WHERE id = 6;
UPDATE products SET primary_image = 'nike-air-max-270.jpg' WHERE id = 7;
UPDATE products SET primary_image = 'adidas-hoodie.jpg' WHERE id = 8;
UPDATE products SET primary_image = 'rayban-aviator.jpg' WHERE id = 9;
UPDATE products SET primary_image = 'fossil-gen6.jpg' WHERE id = 10;

-- Home & Kitchen
UPDATE products SET primary_image = 'philips-air-fryer.jpg' WHERE id = 11;
UPDATE products SET primary_image = 'dyson-v11.jpg' WHERE id = 12;
UPDATE products SET primary_image = 'echo-dot-5.jpg' WHERE id = 13;
UPDATE products SET primary_image = 'prestige-cooktop.jpg' WHERE id = 14;
UPDATE products SET primary_image = 'ikea-desk.jpg' WHERE id = 15;

-- Books
UPDATE products SET primary_image = 'atomic-habits.jpg' WHERE id = 16;
UPDATE products SET primary_image = 'psychology-of-money.jpg' WHERE id = 17;
UPDATE products SET primary_image = 'harry-potter-set.jpg' WHERE id = 18;
UPDATE products SET primary_image = 'think-like-monk.jpg' WHERE id = 19;
UPDATE products SET primary_image = 'rich-dad-poor-dad.jpg' WHERE id = 20;

-- Sports & Fitness
UPDATE products SET primary_image = 'yoga-mat.jpg' WHERE id = 21;
UPDATE products SET primary_image = 'dumbbells-20kg.jpg' WHERE id = 22;
UPDATE products SET primary_image = 'decathlon-cycle.jpg' WHERE id = 23;
UPDATE products SET primary_image = 'nivia-football.jpg' WHERE id = 24;
UPDATE products SET primary_image = 'fitbit-charge-6.jpg' WHERE id = 25;

-- Gaming
UPDATE products SET primary_image = 'ps5-console.jpg' WHERE id = 26;
UPDATE products SET primary_image = 'xbox-series-x.jpg' WHERE id = 27;
UPDATE products SET primary_image = 'logitech-g502.jpg' WHERE id = 28;
UPDATE products SET primary_image = 'razer-blackwidow.jpg' WHERE id = 29;
UPDATE products SET primary_image = 'nintendo-switch.jpg' WHERE id = 30;
```

---

## 🖼️ Image Recommendations:

### Size:
- **Recommended:** 800x800px (square)
- **Minimum:** 500x500px
- **Maximum:** 2000x2000px

### Format:
- **Best:** .jpg or .webp (smaller file size)
- **Good:** .png (transparent background if needed)
- **Avoid:** .bmp, .gif (large file size)

### File Size:
- **Ideal:** 50-200 KB per image
- **Maximum:** 500 KB per image

### Naming Convention:
```
✅ Good: samsung-s23.jpg, iphone-15-pro.jpg
❌ Bad: IMG_1234.jpg, photo (1).jpg, Product Image.jpg
```

---

## 🔍 Verify Images Work:

### Test Image URL:
```
1. Upload image: samsung-s23.jpg
2. Open browser: http://localhost/trendsone/uploads/products/samsung-s23.jpg
3. If image shows → ✅ Working!
4. If 404 error → ❌ Check folder path
```

### Check in Frontend:
```
1. Go to: http://localhost/trendsone/
2. Featured products should show real images
3. No more placeholder images!
```

---

## 🛠️ Troubleshooting:

### Issue 1: Images Not Showing
**Solution:**
```
1. Check folder exists: C:\xampp\htdocs\trendsone\uploads\products\
2. Check image file is there
3. Check database has correct filename
4. Check file permissions (should be readable)
```

### Issue 2: Still Showing Placeholder
**Solution:**
```sql
-- Check if primary_image is set
SELECT id, name, primary_image FROM products;

-- If NULL, update it
UPDATE products SET primary_image = 'your-image.jpg' WHERE id = 1;
```

### Issue 3: Broken Image Icon
**Solution:**
```
1. Check image filename matches database exactly
2. Check file extension (.jpg not .JPG)
3. Check for spaces in filename (use hyphens instead)
```

---

## 📝 Quick Start Checklist:

- [ ] Create `uploads/products/` folder
- [ ] Copy product images to folder
- [ ] Rename images with simple names
- [ ] Update database with image filenames
- [ ] Test image URL in browser
- [ ] Refresh homepage to see real images

---

## 🎯 Example Setup:

### 1. Create Folder:
```
C:\xampp\htdocs\trendsone\uploads\products\
```

### 2. Add 3 Sample Images:
```
samsung-s23.jpg
iphone-15.jpg
sony-headphones.jpg
```

### 3. Update Database:
```sql
UPDATE products SET primary_image = 'samsung-s23.jpg' WHERE id = 1;
UPDATE products SET primary_image = 'iphone-15.jpg' WHERE id = 2;
UPDATE products SET primary_image = 'sony-headphones.jpg' WHERE id = 3;
```

### 4. Test:
```
http://localhost/trendsone/uploads/products/samsung-s23.jpg
```

### 5. View Homepage:
```
http://localhost/trendsone/
```

**Real images should now appear!** 🎉

---

## 📂 Complete Folder Structure:

```
trendsone/
├── uploads/
│   ├── products/          ← Product images here
│   │   ├── samsung-s23.jpg
│   │   ├── iphone-15.jpg
│   │   └── ...
│   ├── categories/        ← Category images
│   ├── blog/             ← Blog post images
│   └── avatars/          ← User profile pictures
├── admin/
├── api/
├── assets/
├── config/
└── ...
```

---

**Your images are now ready to upload!** 🚀

Just create the `uploads/products/` folder, add your images, and update the database!
