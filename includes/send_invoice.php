<?php
// ================================================
// Fungsi Kirim Invoice Email - Serenity's Glow
// ================================================

require_once __DIR__ . '/../libs/PHPMailer/Exception.php';
require_once __DIR__ . '/../libs/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../libs/PHPMailer/SMTP.php';
require_once __DIR__ . '/../config/mail_config.php';
require_once __DIR__ . '/helpers.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Kirim invoice ke email customer.
 * $order  : array data dari tabel orders
 * $items  : array data dari tabel order_items
 * Return  : true kalau berhasil, string pesan error kalau gagal (biar checkout tetap lanjut walau email gagal)
 */
function sendInvoiceEmail($order, $items) {
    $mail = new PHPMailer(true);

    try {
        // ---------- Konfigurasi SMTP ----------
        $mail->isSMTP();
        $mail->Host       = MAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_USERNAME;
        $mail->Password   = MAIL_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = MAIL_PORT;
        $mail->CharSet    = 'UTF-8';

        // ---------- Pengirim & Penerima ----------
        $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
        $mail->addAddress($order['customer_email'], $order['customer_name']);

        // ---------- Isi Email ----------
        $mail->isHTML(true);
        $mail->Subject = "Invoice Pesanan #{$order['id']} - Serenity's Glow";
        $mail->Body    = buildInvoiceHtml($order, $items);
        $mail->AltBody = buildInvoicePlainText($order, $items);

        $mail->send();
        return true;

    } catch (Exception $e) {
        return $mail->ErrorInfo;
    }
}

/**
 * Bangun tampilan HTML invoice (tema soft green & pink)
 */
function buildInvoiceHtml($order, $items) {
    $rows = '';
    foreach ($items as $it) {
        $rows .= "
            <tr>
                <td style='padding:10px; border-bottom:1px solid #f0ecec;'>{$it['product_name']}</td>
                <td style='padding:10px; border-bottom:1px solid #f0ecec; text-align:center;'>{$it['quantity']}</td>
                <td style='padding:10px; border-bottom:1px solid #f0ecec; text-align:right;'>Rp " . number_format($it['price'], 0, ',', '.') . "</td>
                <td style='padding:10px; border-bottom:1px solid #f0ecec; text-align:right;'>Rp " . number_format($it['subtotal'], 0, ',', '.') . "</td>
            </tr>";
    }

    $total = number_format($order['total_amount'], 0, ',', '.');
    $tanggal = date('d M Y, H:i', strtotime($order['created_at']));
    $paymentLabel = paymentMethodLabel($order['payment_method']);

    return "
    <div style='font-family: Poppins, Segoe UI, Arial, sans-serif; max-width:600px; margin:0 auto; background:#fdf9f6; padding:30px; border-radius:16px;'>
        <div style='background:#a8c9a1; padding:20px 30px; border-radius:12px 12px 0 0;'>
            <h2 style='margin:0; color:#fff;'>Serenity's Glow</h2>
        </div>
        <div style='background:#fff; padding:30px; border-radius:0 0 12px 12px;'>
            <p>Halo <strong>{$order['customer_name']}</strong>,</p>
            <p>Terima kasih sudah berbelanja! Berikut detail invoice pesananmu:</p>

            <table style='width:100%; margin:20px 0; font-size:14px;'>
                <tr><td style='padding:4px 0;'><strong>Nomor Pesanan</strong></td><td style='text-align:right;'>#{$order['id']}</td></tr>
                <tr><td style='padding:4px 0;'><strong>Tanggal</strong></td><td style='text-align:right;'>{$tanggal}</td></tr>
                <tr><td style='padding:4px 0;'><strong>Metode Pembayaran</strong></td><td style='text-align:right;'>{$paymentLabel}</td></tr>
                <tr><td style='padding:4px 0;'><strong>Alamat Kirim</strong></td><td style='text-align:right;'>{$order['customer_address']}</td></tr>
            </table>

            <table style='width:100%; border-collapse:collapse; font-size:14px;'>
                <thead>
                    <tr style='background:#f3c9d4;'>
                        <th style='padding:10px; text-align:left;'>Produk</th>
                        <th style='padding:10px; text-align:center;'>Qty</th>
                        <th style='padding:10px; text-align:right;'>Harga</th>
                        <th style='padding:10px; text-align:right;'>Subtotal</th>
                    </tr>
                </thead>
                <tbody>{$rows}</tbody>
            </table>

            <p style='text-align:right; font-size:18px; font-weight:bold; color:#e8a8bb; margin-top:16px;'>
                Total: Rp {$total}
            </p>

            <p style='font-size:13px; color:#999; margin-top:30px;'>
                Status pesanan bisa kamu cek kapan saja di halaman \"Pesanan Saya\" pada akunmu.
            </p>
        </div>
    </div>";
}

/**
 * Versi plain text (buat email client yang nggak support HTML)
 */
function buildInvoicePlainText($order, $items) {
    $text = "INVOICE PESANAN #{$order['id']} - Serenity's Glow\n\n";
    $text .= "Halo {$order['customer_name']},\n";
    $text .= "Terima kasih sudah berbelanja! Berikut detail pesananmu:\n\n";

    foreach ($items as $it) {
        $text .= "- {$it['product_name']} x{$it['quantity']} = Rp " . number_format($it['subtotal'], 0, ',', '.') . "\n";
    }

    $text .= "\nTotal: Rp " . number_format($order['total_amount'], 0, ',', '.') . "\n";
    $text .= "Metode Pembayaran: " . paymentMethodLabel($order['payment_method']) . "\n";
    $text .= "Alamat Kirim: {$order['customer_address']}\n";

    return $text;
}
