# Shopping Cart Troubleshooting Guide

## Quick Start - Test These URLs

### 1. Database Test
Open this URL in browser:
```
http://localhost/lewebprojet/debug.php
```
**What to look for:**
- ✓ Database Connection - should show "Connected to MySQL"
- ✓ Products table - should show 4 products
- ✓ Cart table exists
- ✓ Session ID is displayed

### 2. Cart Test Form
Open this URL in browser:
```
http://localhost/lewebprojet/test_cart.html
```
**What to do:**
- Click "Test Add to Cart" button
- Open Browser Console (F12)
- Look for success or error messages
- Click "Test Form Submit" button

## Step-by-Step Debugging

### Step 1: Open Browser Console
1. Press `F12` key
2. Click "Console" tab
3. Clear console with `console.clear()`
4. Keep this open while testing

### Step 2: Test Direct PHP Request
In console, run:
```javascript
fetch('add_to_cart.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: 'product_id=1&quantity=1'
})
.then(r => r.json())
.then(d => console.log('Response:', d))
.catch(e => console.error('Error:', e))
```

### Step 3: Check PHP Error Logs
Location varies by system:
- Windows: `C:\xampp\apache\logs\error.log` or `C:\php\logs\php_errors.log`
- Linux: `/var/log/apache2/error.log` or `/var/log/php_errors.log`
- Mac: `/var/log/apache2/error_log`

Look for errors related to:
- `add_to_cart.php`
- MySQL connection
- Database queries

### Step 4: Check MySQL Error Log
```bash
# Windows - in XAMPP folder
mysql_error.log

# Linux
sudo tail -f /var/log/mysql/error.log

# Mac
tail -f /var/log/mysql/error.log
```

## Common Issues & Solutions

### Issue: "Product not found"
**Cause:** Product ID 1 doesn't exist in database
**Solution:**
1. Open `debug.php` in browser
2. Check if Products table shows 4 items
3. If empty, refresh page to trigger insert

### Issue: "Invalid product ID or quantity"
**Cause:** Form data not being sent correctly
**Solution:**
1. Check browser console for network errors
2. Verify form has these inputs:
   - `<input type="hidden" name="product_id" value="1">`
   - `<input type="number" name="quantity" value="1">`

### Issue: "Server Error" message
**Cause:** PHP exception in add_to_cart.php
**Solution:**
1. Check server error logs
2. Run `debug.php` to test connection
3. Verify database exists: `stylish_db`

### Issue: Error in Console - "SyntaxError: Unexpected token"
**Cause:** add_to_cart.php returning HTML instead of JSON
**Solution:**
1. Check add_to_cart.php first line has `header('Content-Type: application/json');`
2. Check for any PHP warnings/errors before JSON output
3. Run debug.php to verify database setup

## Files to Check

### JavaScript Files
- ✓ `js/script.js` - Must have `initAddToCart()` function
- ✓ jQuery should be loaded before script.js

### PHP Files  
- ✓ `add_to_cart.php` - Main handler
- ✓ `config.php` - Database connection
- ✓ `cart.php` - Display cart

### HTML Files
- ✓ Form class must be `.shopify-cart` or `.variations-form`
- ✓ Inputs must have names: `product_id`, `quantity`
- ✓ Button type must be `submit`

## Manual Database Setup

If products don't exist, run in phpMyAdmin:

```sql
USE stylish_db;

INSERT INTO products (name, price, image, description) VALUES
('Sport Shoes For Men', 99.00, 'images/single-product-thumb1.jpg', 'Comfortable sport shoes for men'),
('Brand Shoes For Men', 99.00, 'images/single-product-thumb2.jpg', 'Stylish brand shoes for men'),
('Running Shoes For Men', 99.00, 'images/single-product-thumb1.jpg', 'Premium running shoes for men'),
('Women Casual Shoes', 89.00, 'images/single-product-thumb2.jpg', 'Comfortable casual shoes for women');
```

## Test Cart with curl

From command line:
```bash
curl -X POST http://localhost/lewebprojet/add_to_cart.php \
  -d "product_id=1&quantity=1" \
  -H "Content-Type: application/x-www-form-urlencoded"
```

Expected successful response:
```json
{"success":true,"message":"Product added to cart successfully","cart_count":1}
```

## Check Cart Contents

Visit:
```
http://localhost/lewebprojet/cart.php
```

Should show items you added.

## Enable PHP Error Display (Temporary)

If not seeing errors, add to `config.php` after `<?php`:
```php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
```

**Remove this after debugging!**

## What to Report if Still Not Working

When asking for help, provide:
1. Screenshot of debug.php output
2. Console errors (F12 > Console)
3. Server error log errors
4. Output of test_cart.html test buttons
5. MySQL version: `SELECT VERSION();`
6. PHP version check output
