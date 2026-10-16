CREATE DATABASE IF NOT EXISTS db_coffee_simple;
USE db_coffee_simple;

-- Users: admin and customers
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin', 'customer') NOT NULL DEFAULT 'customer'
);

-- Products managed by the admin
CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  description VARCHAR(255) NOT NULL DEFAULT '',
  price INT NOT NULL,
  image VARCHAR(255) NULL
);

-- Orders: user_id is NULL when a guest orders
CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  customer_name VARCHAR(100) NOT NULL,
  subtotal INT NOT NULL,
  discount INT NOT NULL DEFAULT 0,
  total INT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Items inside each order (name and price are copied so old orders never change)
CREATE TABLE order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NULL,
  product_name VARCHAR(100) NOT NULL,
  price INT NOT NULL,
  qty INT NOT NULL,
  CONSTRAINT fk_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  CONSTRAINT fk_items_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);

-- Admin account: username admin, password admin123
INSERT INTO users (username, password, role)
VALUES ('admin', '$2y$12$7oKtyeg9gtpPIElBltPmu.GXAz77WYquP/wr7IQbKbuitBTc3LTsm', 'admin');

-- Sample products
INSERT INTO products (name, description, price, image) VALUES
('Espresso', 'Short, strong double shot.', 18000, 'sample-espresso.svg'),
('Americano', 'Espresso with hot water.', 22000, 'sample-americano.svg'),
('Caffe Latte', 'Espresso with steamed milk.', 28000, 'sample-latte.svg'),
('Cappuccino', 'Espresso, milk, and thick foam.', 28000, 'sample-cappuccino.svg'),
('Flat White', 'Ristretto with velvety milk.', 30000, 'sample-flatwhite.svg'),
('Iced Palm Sugar Latte', 'Espresso, milk, and palm sugar over ice.', 32000, 'sample-iced.svg');
