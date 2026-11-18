CREATE DATABASE IF NOT EXISTS fragrance_shop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE fragrance_shop;
CREATE TABLE IF NOT EXISTS users (
 id INT AUTO_INCREMENT PRIMARY KEY, full_name VARCHAR(120) NOT NULL, email VARCHAR(190) NOT NULL UNIQUE,
 phone VARCHAR(30) NOT NULL, address TEXT NOT NULL, password_hash VARCHAR(255) NOT NULL,
 member_code VARCHAR(30) UNIQUE DEFAULT NULL,
 registration_status ENUM('unpaid','pending_review','paid') NOT NULL DEFAULT 'unpaid',
 payment_reference VARCHAR(120) DEFAULT NULL, payment_proof VARCHAR(255) DEFAULT NULL,
 payment_submitted_at DATETIME DEFAULT NULL, registration_payment_id VARCHAR(80) DEFAULT NULL UNIQUE,
 registration_paid_at DATETIME DEFAULT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS products (
 id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(150) NOT NULL,
 category ENUM('Men Perfumes','Women Perfumes','Home Diffusers','Car Diffusers','Car & Home Fresheners') NOT NULL,
 description TEXT, price DECIMAL(10,2) NOT NULL DEFAULT 0, image VARCHAR(255) DEFAULT NULL,
 active TINYINT(1) NOT NULL DEFAULT 1, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS orders (
 id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, member_name VARCHAR(120) NOT NULL,
 member_code VARCHAR(30) NOT NULL, contact VARCHAR(30) NOT NULL, delivery_address TEXT NOT NULL,
 total DECIMAL(10,2) NOT NULL DEFAULT 0,
 status ENUM('Pending','Processing','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
 payment_status ENUM('Pending','Paid','Failed') NOT NULL DEFAULT 'Pending',
 payment_id VARCHAR(80) DEFAULT NULL UNIQUE, payfast_payment_id VARCHAR(80) DEFAULT NULL,
 paid_at DATETIME DEFAULT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS order_items (
 id INT AUTO_INCREMENT PRIMARY KEY, order_id INT NOT NULL, product_id INT NOT NULL,
 product_name VARCHAR(150) NOT NULL, unit_price DECIMAL(10,2) NOT NULL, quantity INT NOT NULL,
 line_total DECIMAL(10,2) NOT NULL, FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS payment_notifications (
 id INT AUTO_INCREMENT PRIMARY KEY, payment_id VARCHAR(80) NOT NULL, payfast_payment_id VARCHAR(80) DEFAULT NULL,
 payment_status VARCHAR(30) DEFAULT NULL, raw_payload TEXT, received_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY unique_payment_notification (payment_id, payfast_payment_id, payment_status)
);
