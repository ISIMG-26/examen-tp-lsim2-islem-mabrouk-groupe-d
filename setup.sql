-- Create database
CREATE DATABASE IF NOT EXISTS stylish_db;

-- Use the database
USE stylish_db;

-- Create products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create cart table (for simplicity, assuming one cart per session or user)
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    session_id VARCHAR(255), -- or user_id if you have users
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Create users table for registration
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample products
INSERT INTO products (name, price, image, description) VALUES
('Sport Shoes For Men', 99.00, 'images/single-product-thumb1.jpg', 'Comfortable sport shoes for men'),
('Brand Shoes For Men', 99.00, 'images/single-product-thumb2.jpg', 'Stylish brand shoes for men');

-- Insert sample cart items (for demo)
INSERT INTO cart (product_id, quantity, session_id) VALUES
(1, 1, 'demo_session'),
(2, 1, 'demo_session');