<?php
// ================================================
// Konfigurasi Email (Gmail SMTP) - Serenity's Glow
// ================================================

// GANTI dengan Gmail kamu
define('MAIL_FROM_ADDRESS', 'emailkamu@gmail.com');
define('MAIL_FROM_NAME', "Serenity's Glow");

// GANTI dengan Gmail kamu juga (buat login SMTP)
define('MAIL_USERNAME', 'emailkamu@gmail.com');

// JANGAN pakai password Gmail biasa!
// Ini harus App Password 16 karakter dari Google (cara bikinnya ada di panduan)
define('MAIL_PASSWORD', 'xxxx xxxx xxxx xxxx');

define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 587);
