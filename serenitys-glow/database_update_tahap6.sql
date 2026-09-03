-- ================================================
-- Update Database - Tahap 6
-- Tambah kolom metode pembayaran di tabel orders
-- Jalankan ini di phpMyAdmin, di database serenitys_glow yang sudah ada
-- ================================================

USE serenitys_glow;

ALTER TABLE orders
ADD COLUMN payment_method ENUM('transfer_bank', 'cod', 'e_wallet') NOT NULL DEFAULT 'transfer_bank' AFTER customer_address;
