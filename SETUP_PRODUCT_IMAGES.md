# Setup Product Images - Complete Guide

## 🔍 Problem Identified:

**Wrong Path:**
```
http://localhost/trendsone/uploads/products/uploads/products/samsung-galaxy-s23-ultra-1.jpg
```

**Correct Path:**
```
http://localhost/trendsone/uploads/products/samsung-s23.jpg
```

**Root Cause:** The `primary_image` column doesn't exist in the products table yet.

---

## ✅ Solution (3 Steps):

### Step 1: Add primary_image Column to Database

**Go to phpMyAdmin:**
```
http://localhost/phpmyadmin
```

**Run this SQL:**
```sql
-- Import the file: database/add-product-images.sql
-- OR run this command directly:

ALTER TABLE `products` ADD COLUMN `primary_image` VARCHAR(255) DEFAULT NULL AFTER `long_description`;
```

---

### Step 2: Create Upload Folder

**Create folder structure:**
```
C:\xampp\htdocs\trendsone\uploads\products\
```

**In Command Prompt (Run as Administrator):**
```bash
cd C:\xampp\htdocs\trendsone
mkdir uploads
cd uploads
mkdir products
```

---

### Step 3: Update Products with Image Names

**In phpMyAdmin, run:**
```sql
-- Electronics
UPDATE products SET primary_image = 'samsung-galaxy-s23-ultra.jpg' WHERE slug = 'samsung-galaxy-s23-ultra';
UPDATE products SET primary_image = 'apple-iphone-15-pro.jpg' WHERE slug = 'apple-iphone-15-pro';
UPDATE products SET primary_image = 'sony-wh-1000xm5.jpg' WHERE slug = 'sony-wh-1000xm5-headphones';
UPDATE products SET primary_image = 'dell-xps-15.jpg' WHERE slug = 'dell-xps-15-laptop';
UPDATE products SET primary_image = 'apple-watch-series-9.jpg' WHERE slug = 'apple-watch-series-9';

-- Fashion
UPDATE products SET primary_image = 'levis-511-jeans.jpg' WHERE slug = 'levis-511-slim-fit-jeans';
UPDATE products SET primary_image = 'nike-air-max-270.jpg' WHERE slug = 'nike-air-max-270';
UPDATE products SET primary_image = 'adidas-hoodie.jpg' WHERE slug = 'adidas-originals-hoodie';
UPDATE products SET primary_image = 'rayban-aviator.jpg' WHERE slug = 'ray-ban-aviator-sunglasses';
UPDATE products SET primary_image = 'fossil-gen-6.jpg' WHERE slug = 'fossil-gen-6-smartwatch';

-- Home & Living
UPDATE products SET primary_image = 'philips-air-fryer.jpg' WHERE slug = 'philips-air-fryer';
UPDATE products SET primary_image = 'dyson-v11.jpg' WHERE slug = 'dyson-v11-vacuum-cleaner';
UPDATE products SET primary_image = 'echo-dot-5.jpg' WHERE slug = 'amazon-echo-dot-5th-gen';
UPDATE products SET primary_image = 'prestige-cooktop.jpg' WHERE slug = 'prestige-induction-cooktop';
UPDATE products SET primary_image = 'ikea-desk.jpg' WHERE slug = 'ikea-study-table';

-- Books
UPDATE products SET primary_image = 'atomic-habits.jpg' WHERE slug = 'atomic-habits-james-clear';
UPDATE products SET primary_image = 'psychology-of-money.jpg' WHERE slug = 'psychology-of-money';
UPDATE products SET primary_image = 'harry-potter-set.jpg' WHERE slug = 'harry-potter-complete-collection';
UPDATE products SET primary_image = 'think-like-monk.jpg' WHERE slug = 'think-like-a-monk';
UPDATE products SET primary_image = 'rich-dad-poor-dad.jpg' WHERE slug = 'rich-dad-poor-dad';

-- Sports
UPDATE products SET primary_image = 'yoga-mat.jpg' WHERE slug = 'yoga-mat-premium';
UPDATE products SET primary_image = 'dumbbells-20kg.jpg' WHERE slug = 'dumbbells-set-20kg';
UPDATE products SET primary_image = 'decathlon-cycle.jpg' WHERE slug = 'decathlon-cycle-26t';
UPDATE products SET primary_image = 'nivia-football.jpg' WHERE slug = 'nivia-football-size-5';
UPDATE products SET primary_image = 'fitbit-charge-6.jpg' WHERE slug = 'fitbit-charge-6';

-- Gaming
UPDATE products SET primary_image = 'ps5-console.jpg' WHERE slug = 'playstation-5-console';
UPDATE products SET primary_image = 'xbox-series-x.jpg' WHERE slug = 'xbox-series-x';
UPDATE products SET primary_image = 'logitech-g502.jpg' WHERE slug = 'logitech-g502-gaming-mouse';
UPDATE products SET primary_image = 'razer-blackwidow.jpg' WHERE slug = 'razer-blackwidow-keyboard';
UPDATE products SET primary_image = 'nintendo-switch.jpg' WHERE slug = 'nintendo-switch-oled';
```

**OR Import the SQL file:**
```
database/add-product-images.sql
```

---

## 📂 Upload Your Images:

### Image Names Required:

**Electronics:**
- samsung-galaxy-s23-ultra.jpg
- apple-iphone-15-pro.jpg
- sony-wh-1000xm5.jpg
- dell-xps-15.jpg
- apple-watch-series-9.jpg

**Fashion:**
- levis-511-jeans.jpg
- nike-air-max-270.jpg
- adidas-hoodie.jpg
- rayban-aviator.jpg
- fossil-gen-6.jpg

**Home & Living:**
- philips-air-fryer.jpg
- dyson-v11.jpg
- echo-dot-5.jpg
- prestige-cooktop.jpg
- ikea-desk.jpg

**Books:**
- atomic-habits.jpg
- psychology-of-money.jpg
- harry-potter-set.jpg
- think-like-monk.jpg
- rich-dad-poor-dad.jpg

**Sports:**
- yoga-mat.jpg
- dumbbells-20kg.jpg
- decathlon-cycle.jpg
- nivia-football.jpg
- fitbit-charge-6.jpg

**Gaming:**
- ps5-console.jpg
- xbox-series-x.jpg
- logitech-g502.jpg
- razer-blackwidow.jpg
- nintendo-switch.jpg

---

## 🔍 How It Works:

### Database Stores:
```sql
primary_image = 'samsung-galaxy-s23-ultra.jpg'  -- Just filename
```

### PHP Code Builds Full Path:
```php
PRODUCT_IMAGE_URL . $product['primary_image']

// PRODUCT_IMAGE_URL = http://localhost/trendsone/uploads/products/
// primary_image = samsung-galaxy-s23-ultra.jpg
// Result = http://localhost/trendsone/uploads/products/samsung-galaxy-s23-ultra.jpg
```

### Code in index.php & shop.php:
```php
<img src="<?php echo $product['primary_image'] 
    ? PRODUCT_IMAGE_URL . $product['primary_image'] 
    : 'https://via.placeholder.com/300x250?text=No+Image'; ?>" 
     class="product-image" alt="<?php echo htmlspecialchars($product['name']); ?>">
```

**✅ This code is already correct! No changes needed.**

---

## ✅ Quick Setup Checklist:

1. [ ] Run SQL to add `primary_image` column
2. [ ] Create `uploads/products/` folder
3. [ ] Run SQL to update product image names
4. [ ] Upload your product images to `uploads/products/`
5. [ ] Refresh homepage - images should appear!

---

## 🧪 Test Your Setup:

### Step 1: Check Column Exists
```sql
DESCRIBE products;
-- Should show 'primary_image' column
```

### Step 2: Check Image Names
```sql
SELECT id, name, primary_image FROM products LIMIT 5;
-- Should show filenames like 'samsung-galaxy-s23-ultra.jpg'
```

### Step 3: Test Image URL
```
http://localhost/trendsone/uploads/products/samsung-galaxy-s23-ultra.jpg
-- Should show the image (after you upload it)
```

### Step 4: Check Homepage
```
http://localhost/trendsone/
-- Products should show real images instead of placeholders
```

---

## 📋 Complete SQL Import (All in One):

**File:** `database/add-product-images.sql`

**Import via phpMyAdmin:**
1. Go to http://localhost/phpmyadmin
2. Select `trendsone_db` database
3. Click "Import" tab
4. Choose file: `database/add-product-images.sql`
5. Click "Go"
6. Done! ✅

---

## 🎯 Summary:

**Problem:** `primary_image` column missing from database
**Solution:** Add column + Update with image filenames
**Result:** Images will display correctly at:
```
http://localhost/trendsone/uploads/products/[filename].jpg
```

**No code changes needed in index.php or shop.php - they're already correct!** ✅

---

## 📞 Troubleshooting:

### Issue: Still showing placeholders
**Check:**
```sql
SELECT primary_image FROM products WHERE id = 31;
-- Should return: samsung-galaxy-s23-ultra.jpg
-- If NULL, run the UPDATE queries
```

### Issue: Broken image icon
**Check:**
1. File exists: `C:\xampp\htdocs\trendsone\uploads\products\samsung-galaxy-s23-ultra.jpg`
2. Filename matches database exactly (case-sensitive)
3. File extension is correct (.jpg not .JPG)

### Issue: 404 error
**Check:**
1. Folder exists: `C:\xampp\htdocs\trendsone\uploads\products\`
2. Apache has read permissions
3. URL is correct: `http://localhost/trendsone/uploads/products/filename.jpg`

---

**Your product images are now ready to work correctly!** 🎉
