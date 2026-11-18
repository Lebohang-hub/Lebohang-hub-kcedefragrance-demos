-- Run this ONCE in phpMyAdmin if you already have an existing fragrance_shop database.
USE fragrance_shop;
ALTER TABLE users ADD COLUMN registration_payment_id VARCHAR(80) DEFAULT NULL UNIQUE;
ALTER TABLE users ADD COLUMN registration_paid_at DATETIME DEFAULT NULL;
ALTER TABLE orders ADD COLUMN payment_status ENUM('Pending','Paid','Failed') NOT NULL DEFAULT 'Pending';
ALTER TABLE orders ADD COLUMN payment_id VARCHAR(80) DEFAULT NULL UNIQUE;
ALTER TABLE orders ADD COLUMN payfast_payment_id VARCHAR(80) DEFAULT NULL;
ALTER TABLE orders ADD COLUMN paid_at DATETIME DEFAULT NULL;
CREATE TABLE IF NOT EXISTS payment_notifications (
 id INT AUTO_INCREMENT PRIMARY KEY, payment_id VARCHAR(80) NOT NULL, payfast_payment_id VARCHAR(80) DEFAULT NULL,
 payment_status VARCHAR(30) DEFAULT NULL, raw_payload TEXT, received_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY unique_payment_notification (payment_id, payfast_payment_id, payment_status)
);
