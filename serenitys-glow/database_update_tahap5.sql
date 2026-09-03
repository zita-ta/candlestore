-- ================================================
-- Update Database - Tahap 5
-- Tabel untuk sistem pesanan (orders) & keranjang checkout
-- Jalankan ini di phpMyAdmin, di database serenitys_glow yang sudah ada
-- ================================================

USE serenitys_glow;

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    customer_address TEXT,
    payment_method ENUM('transfer_bank', 'cod', 'e_wallet') NOT NULL DEFAULT 'transfer_bank',
    total_amount DECIMAL(12,2) NOT NULL,
    status ENUM('pending', 'diproses', 'dikirim', 'selesai', 'dibatalkan') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(100) NOT NULL, -- disimpan terpisah, jaga-jaga produk dihapus/diedit nanti
    price DECIMAL(10,2) NOT NULL,       -- harga saat transaksi, bukan harga produk saat ini
    quantity INT NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
