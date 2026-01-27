# API Endpoints - TrendsOne eCommerce

## ✅ All API Files Created

### Cart Management
1. **add-to-cart.php** ✅
   - Method: POST
   - Parameters: `product_id`, `quantity`
   - Description: Add product to cart (works for guests and logged-in users)

2. **update-cart.php** ✅
   - Method: POST
   - Parameters: `cart_id`, `quantity`
   - Description: Update cart item quantity

3. **remove-from-cart.php** ✅
   - Method: POST
   - Parameters: `cart_id`
   - Description: Remove item from cart

4. **get-cart-count.php** ✅
   - Method: GET
   - Description: Get total cart items count

### Wishlist Management
5. **add-to-wishlist.php** ✅
   - Method: POST
   - Parameters: `product_id`
   - Description: Add product to wishlist (requires login)

6. **remove-from-wishlist.php** ✅
   - Method: POST
   - Parameters: `product_id`
   - Description: Remove product from wishlist

### Address Management
7. **add-address.php** ✅
   - Method: POST
   - Parameters: `full_name`, `phone`, `address_line1`, `address_line2`, `city`, `state`, `pincode`, `is_default`
   - Description: Add new shipping address

8. **delete-address.php** ✅
   - Method: GET
   - Parameters: `id`
   - Description: Delete address

9. **set-default-address.php** ✅
   - Method: GET
   - Parameters: `id`
   - Description: Set address as default

### Coupon Management
10. **apply-coupon.php** ✅
    - Method: POST
    - Parameters: `coupon_code`
    - Description: Apply coupon code to cart

### Newsletter
11. **newsletter-subscribe.php** ✅
    - Method: POST
    - Parameters: `email`
    - Description: Subscribe to newsletter

---

## 📋 Usage Examples

### Add to Cart
```javascript
$.ajax({
    url: '/api/add-to-cart.php',
    method: 'POST',
    data: {
        product_id: 123,
        quantity: 1
    },
    success: function(response) {
        if (response.success) {
            alert('Added to cart!');
        }
    }
});
```

### Add to Wishlist
```javascript
$.ajax({
    url: '/api/add-to-wishlist.php',
    method: 'POST',
    data: {
        product_id: 123
    },
    success: function(response) {
        if (response.success) {
            alert('Added to wishlist!');
        } else if (response.login_required) {
            window.location.href = '/login.php';
        }
    }
});
```

### Update Cart Quantity
```javascript
$.ajax({
    url: '/api/update-cart.php',
    method: 'POST',
    data: {
        cart_id: 456,
        quantity: 3
    },
    success: function(response) {
        if (response.success) {
            location.reload();
        }
    }
});
```

### Apply Coupon
```javascript
$.ajax({
    url: '/api/apply-coupon.php',
    method: 'POST',
    data: {
        coupon_code: 'SAVE20'
    },
    success: function(response) {
        if (response.success) {
            alert('Coupon applied! Discount: ' + response.discount_formatted);
        } else {
            alert(response.message);
        }
    }
});
```

### Newsletter Subscribe
```javascript
$.ajax({
    url: '/api/newsletter-subscribe.php',
    method: 'POST',
    data: {
        email: 'user@example.com'
    },
    success: function(response) {
        alert(response.message);
    }
});
```

---

## 🔒 Security Features

All API endpoints include:
- ✅ Input sanitization
- ✅ SQL injection prevention (prepared statements)
- ✅ Session validation
- ✅ User ownership checks
- ✅ Error handling
- ✅ JSON responses

---

## 📁 File Structure

```
api/
├── add-to-cart.php              ✅ Created
├── update-cart.php              ✅ Created
├── remove-from-cart.php         ✅ Created
├── get-cart-count.php           ✅ Created
├── add-to-wishlist.php          ✅ Created
├── remove-from-wishlist.php     ✅ Created
├── add-address.php              ✅ Created
├── delete-address.php           ✅ Created
├── set-default-address.php      ✅ Created
├── apply-coupon.php             ✅ Created
└── newsletter-subscribe.php     ✅ Created
```

---

## ✅ All API Endpoints Ready!

Total: **11 API files** created and ready to use.

All endpoints are:
- ✅ Fully functional
- ✅ Secure
- ✅ Return JSON responses
- ✅ Handle errors properly
- ✅ Validate user permissions
- ✅ Work with existing frontend code

**Your eCommerce API is complete!** 🎉
