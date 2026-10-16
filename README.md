# Coffee Shop 

Coffee ordering app for the case study: the admin manages products, images, and prices; registered customers who are logged in get 10% off; guests pay the regular price.

Stack: HTML, Bootstrap 5, PHP (procedural MySQLi), MariaDB/MySQL (XAMPP).

## Setup
1. Start Apache and MySQL in XAMPP.
2. Copy this folder to `C:\xampp\htdocs\coffee-shop_Valian` (so `index.php` is directly inside it).
3. In phpMyAdmin, open the SQL tab, paste all of `database.sql`, click Go.
4. Open `http://localhost/coffee-shop_Valian/`.

## Accounts
| Role | Username | Password |
|------|----------|----------|
| Admin | admin | admin123 |
| Customer | register on the site | your choice |

## Discount rule
In `order.php`: `if (is_customer()) { $discount = round($subtotal * 10 / 100); }`
Prices are always read from the database, never from the form.

## Files
- `config.php` database connection, session, small helpers
- `navbar.php` shared navbar
- `index.php` menu and order form
- `order.php` saves the order, applies the discount, shows the receipt
- `register.php`, `login.php`, `logout.php`
- `admin/index.php` product list and orders
- `admin/product_form.php` add/edit product with image upload and price
- `admin/delete.php` delete product
- `database.sql` tables, admin account, sample products

Name: Valian Athalla Syahputra · Student ID: 5025251156
