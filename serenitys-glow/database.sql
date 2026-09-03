-- ================================================
-- Database: serenitys_glow
-- Website Serenity's Glow - Toko Lilin Aromaterapi
-- ================================================

CREATE DATABASE IF NOT EXISTS serenitys_glow;
USE serenitys_glow;

-- Tabel users (untuk admin & customer, login di halaman yang sama)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'customer') NOT NULL DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel products (buat katalog & slideshow di landing page)
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    image VARCHAR(255) DEFAULT NULL,
    is_featured TINYINT(1) DEFAULT 0, -- 1 = tampil di slideshow landing page
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Akun admin default
-- Email: admin@serenitysglow.com | Password: admin123
INSERT INTO users (name, email, password, role) VALUES
('Admin Serenity', 'admin@serenitysglow.com', '$2b$10$6Nt2tEbHK/A3oeOu1EMfgOlwIgR5YrnEu0Rji/IUgn6S6q.fZDera', 'admin');

-- Contoh produk
INSERT INTO products (name, description, price, stock, image, is_featured) VALUES
('Lavender Dream', 'Lilin aromaterapi lavender untuk relaksasi dan tidur nyenyak', 45000, 20, 'lavender.jpg', 1),
('Rose Serenity', 'Lilin aroma mawar lembut, cocok untuk suasana romantis', 50000, 15, 'rose.jpg', 1),
('Vanilla Bliss', 'Lilin aroma vanila hangat, menenangkan pikiran', 40000, 25, 'vanilla.jpg', 1),
('Citrus Fresh', 'Lilin aroma jeruk segar, membangkitkan semangat', 42000, 18, 'citrus.jpg', 0);
