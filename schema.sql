-- Run this once to create the database and table
CREATE DATABASE IF NOT EXISTS crud_paypal_demo;
USE crud_paypal_demo;

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description VARCHAR(500),
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- A couple of sample rows so the video has something to show right away
INSERT INTO products (name, description, price) VALUES
('Wireless Mouse', 'Ergonomic wireless mouse', 12.99),
('Mechanical Keyboard', 'RGB backlit mechanical keyboard', 45.50);
