# Shopping Cart Setup & Testing Guide

## 1. DATABASE SETUP (Required First)

### Option A: Using phpMyAdmin
1. Open phpMyAdmin (usually at http://localhost/phpmyadmin)
2. Click "SQL" tab
3. Copy and paste all content from `setup.sql`
4. Click "Go" to execute

### Option B: Using Command Line
```bash
mysql -u root < setup.sql
```

### Option C: Automatic (First Page Load)
If using the existing config.php, the database will be created automatically but tables won't exist. You still need to run the SQL.

---

## 2. VERIFY DATABASE TABLES CREATED

In phpMyAdmin, check that these tables exist:
- `stylish_db.products` (should have 2 sample products)
- `stylish_db.cart` (for storing cart items)
- `stylish_db.users` (for registration)

---

## 3. TEST CART FUNCTIONALITY

### Test 1: Add Product to Cart
1. Open `index.html` in browser
2. Find the modal with "Running Shoes For Men" (or click a product to open modal)
3. Change quantity if desired
4. Click "Add to cart" button
5. **Expected**: Alert shows "Product added to cart! (1 items)"

### Test 2: Add Same Product Again
1. Add the same product again
2. **Expected**: Quantity increases to 2 instead of creating duplicate

### Test 3: View Cart
1. Navigate to `cart.php`
2. **Expected**: 
   - Shows all items in cart
   - Displays quantity for each item
   - Shows subtotal, tax (8%), and total

### Test 4: Remove from Cart
1. In `cart.php`, look for a remove button next to items
2. Click it and confirm removal
3. **Expected**: Item removed and cart recalculates

### Test 5: Update Quantity
1. In `cart.php`, find quantity input fields
2. Change quantity and it should auto-update
3. **Expected**: Subtotal, tax, and total recalculate automatically

---

## 4. HOW TO ADD MORE PRODUCTS

Add to `setup.sql` or insert via phpMyAdmin:
```sql
INSERT INTO products (name, price, image, description) VALUES
('Product Name', 99.00, 'images/product.jpg', 'Description here');
```

---

## 5. HOW TO USE IN PRODUCT PAGES

Add this to the form in any product page (men.html, women.html, etc.):
```html
<input type="hidden" name="product_id" value="PRODUCT_ID_HERE">
```

Replace `PRODUCT_ID_HERE` with the actual product ID from database.

---

## 6. TESTING NOTES

- **Cart is session-based**: Each browser session has separate cart
- **Persistence**: Cart data stays in database until session expires
- **No login required**: Demo allows adding to cart without logging in
- **Product ID**: Defaults to 1 if not specified, for demo purposes

---

## 7. TROUBLESHOOTING

### Issue: "Product added to cart" but nothing happens
- Check browser console (F12 → Console) for JavaScript errors
- Verify jQuery is loaded before script.js
- Check that add_to_cart.php exists and is readable

### Issue: Cart shows empty
- Verify database connection in config.php
- Check that stylish_db database exists
- Run setup.sql again to create tables

### Issue: "Error adding product to cart"
- Check PHP error logs
- Verify MySQL connection is working
- Ensure product_id matches actual product in database

### Issue: Session not working
- Clear browser cookies
- Try incognito/private window
- Check php.ini has session.save_path configured

---

## 8. FILES REFERENCE

### New Cart Files:
- `add_to_cart.php` - AJAX endpoint for adding to cart
- `remove_from_cart.php` - AJAX endpoint for removing items
- `update_cart.php` - AJAX endpoint for updating quantities

### Updated Files:
- `js/script.js` - Added cart event handlers
- `cart.php` - Uses proper session management
- `index.html` - Added product_id field
- `config.php` - Already has database config

### Original Files (Unchanged):
- `setup.sql` - Database schema
- HTML product pages (men.html, women.html, etc.)
