# 🛍️ Import Sample Products - Complete Guide

## Why Dummy Products Are Showing on Homepage?

The homepage shows **dummy/sample products** because:
1. ❌ Database is empty (no products in database)
2. ❌ Sample products not imported yet
3. ✅ Homepage has fallback dummy data to show something when database is empty

---

## 🚀 Quick Fix - Import Sample Products

### Step 1: Import Database Schema (If Not Done)
```
1. Open: http://localhost/phpmyadmin
2. Create database: trendsone_db
3. Click on trendsone_db
4. Go to "Import" tab
5. Choose file: C:\xampp\htdocs\trendsone\database\schema.sql
6. Click "Go"
```

### Step 2: Import Sample Products
```
1. Stay in phpMyAdmin
2. Make sure trendsone_db is selected
3. Go to "Import" tab again
4. Choose file: C:\xampp\htdocs\trendsone\database\sample-products.sql
5. Click "Go"
6. Wait for success message
```

### Step 3: Verify Products
```sql
-- Run this query in phpMyAdmin SQL tab:
SELECT COUNT(*) as total_products FROM products;

-- Should show: 30 products
```

### Step 4: Refresh Homepage
```
1. Go to: http://localhost/trendsone/
2. Refresh page (Ctrl + F5)
3. You should now see REAL products instead of dummy ones!
```

---

## 📦 What Products Are Included?

### 30 Sample Products Across 6 Categories:

**Electronics (5 products):**
- Samsung Galaxy S23 Ultra - ₹109,999
- Apple iPhone 15 Pro - ₹129,900
- Sony WH-1000XM5 Headphones - ₹24,990
- Dell XPS 15 Laptop - ₹149,999
- Apple Watch Series 9 - ₹39,900

**Fashion (5 products):**
- Levi's 511 Slim Fit Jeans - ₹2,999
- Nike Air Max 270 - ₹9,995
- Adidas Originals Hoodie - ₹3,499
- Ray-Ban Aviator Sunglasses - ₹6,999
- Fossil Gen 6 Smartwatch - ₹18,995

**Home & Kitchen (5 products):**
- Philips Air Fryer - ₹9,995
- Dyson V11 Vacuum Cleaner - ₹44,900
- Amazon Echo Dot 5th Gen - ₹3,999
- Prestige Induction Cooktop - ₹2,799
- IKEA Study Table - ₹7,499

**Books (5 products):**
- Atomic Habits by James Clear - ₹449
- The Psychology of Money - ₹299
- Harry Potter Complete Collection - ₹3,999
- Think Like a Monk - ₹349
- Rich Dad Poor Dad - ₹299

**Sports & Fitness (5 products):**
- Yoga Mat Premium - ₹999
- Dumbbells Set 20kg - ₹2,999
- Decathlon Cycle 26T - ₹12,999
- Nivia Football Size 5 - ₹699
- Fitbit Charge 6 - ₹12,999

**Gaming (5 products):**
- PlayStation 5 Console - ₹49,990
- Xbox Series X - ₹47,990
- Logitech G502 Gaming Mouse - ₹3,995
- Razer BlackWidow Keyboard - ₹6,999
- Nintendo Switch OLED - ₹32,999

---

## ✨ Product Features

Each product includes:
- ✅ Name, slug, SKU
- ✅ Category
- ✅ Price and sale price
- ✅ Short and full description
- ✅ Stock quantity
- ✅ Brand
- ✅ Featured/New arrival flags
- ✅ Discount percentage (auto-calculated)
- ✅ Sample reviews

---

## 🎯 After Importing Products

### Homepage Will Show:
1. **Hero Slider** - 3 promotional banners
2. **Categories** - 6 real categories from database
3. **Featured Products** - 8 products marked as featured
4. **New Arrivals** - 8 products marked as new arrivals

### Shop Page Will Show:
- All 30 products with filters
- Category filtering
- Price range filtering
- Sorting options
- Pagination

### Product Detail Page Will Show:
- Full product information
- Sample reviews
- Related products
- Add to cart functionality

---

## 📸 Adding Product Images

Products are created without images. To add images:

### Option 1: Via Admin Panel
```
1. Login: http://localhost/trendsone/admin/
2. Go to Products → All Products
3. Click "Edit" on any product
4. Upload product images
5. Set primary image
6. Save
```

### Option 2: Manually Add Images
```
1. Create folder: uploads/products/
2. Add product images (e.g., samsung-s23.jpg)
3. Update database:

UPDATE products 
SET primary_image = 'samsung-s23.jpg' 
WHERE slug = 'samsung-galaxy-s23-ultra';
```

### Option 3: Use Placeholder Images
Products without images will show placeholder images automatically:
```
https://via.placeholder.com/300x250?text=Product+Name
```

---

## 🔄 Reset and Re-import

If you want to start fresh:

```sql
-- Delete all products
DELETE FROM products;
DELETE FROM product_reviews;

-- Reset auto increment
ALTER TABLE products AUTO_INCREMENT = 1;
ALTER TABLE product_reviews AUTO_INCREMENT = 1;

-- Re-import sample-products.sql
```

---

## 🎨 Customize Products

### Add More Products via Admin:
```
1. Login to admin panel
2. Go to Products → Add New
3. Fill in product details
4. Upload images
5. Set category, price, stock
6. Mark as featured/new arrival
7. Save
```

### Edit Existing Products:
```
1. Go to Products → All Products
2. Click "Edit" on any product
3. Modify details
4. Save changes
```

---

## 📊 Database Tables Populated

After importing, these tables will have data:

| Table | Records |
|-------|---------|
| products | 30 |
| product_reviews | 8 |
| categories | 6 (from schema.sql) |

---

## ✅ Verification Checklist

After importing, verify:

- [ ] Homepage shows real products (not dummy)
- [ ] Shop page shows all 30 products
- [ ] Categories filter works
- [ ] Product detail pages open
- [ ] Featured products section shows products
- [ ] New arrivals section shows products
- [ ] Product reviews are visible
- [ ] Add to cart works

---

## 🐛 Troubleshooting

### Issue: Still showing dummy products
**Solution:**
1. Clear browser cache (Ctrl + Shift + Delete)
2. Hard refresh (Ctrl + F5)
3. Check if products exist:
   ```sql
   SELECT COUNT(*) FROM products;
   ```

### Issue: Products imported but not showing
**Solution:**
1. Check product status:
   ```sql
   SELECT COUNT(*) FROM products WHERE status = 'active';
   ```
2. Make sure status is 'active' not 'draft'

### Issue: Import failed
**Solution:**
1. Check if categories exist first
2. Import schema.sql before sample-products.sql
3. Check phpMyAdmin error message
4. Make sure database is selected

---

## 🎉 Success!

Once imported successfully:
- ✅ 30 products ready to use
- ✅ Homepage shows real products
- ✅ Shop page fully functional
- ✅ Categories working
- ✅ Search working
- ✅ Filters working
- ✅ Ready for customers!

---

## 📝 Next Steps

1. ✅ Import products (done)
2. 📸 Add product images
3. 🎨 Customize product descriptions
4. 💰 Adjust prices
5. 📦 Update stock quantities
6. ⭐ Add more products
7. 🚀 Launch your store!

---

**File Location:** `database/sample-products.sql`
**Import Time:** ~5 seconds
**Products:** 30
**Categories:** 6
**Reviews:** 8

**Your store is now ready with sample products!** 🎊
