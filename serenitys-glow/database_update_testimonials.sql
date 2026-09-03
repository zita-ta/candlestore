-- ================================================
-- Update Database - Testimoni
-- Jalankan ini di phpMyAdmin, di database serenitys_glow yang sudah ada
-- ================================================

USE serenitys_glow;

CREATE TABLE testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_id INT NOT NULL,
    customer_name VARCHAR(100) NOT NULL,
    rating TINYINT NOT NULL, -- 1 sampai 5
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (order_id) REFERENCES orders(id),
    UNIQUE KEY unique_order_review (order_id) -- 1 pesanan cuma boleh 1 ulasan
);
