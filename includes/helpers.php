<?php
// ================================================
// Helper Umum - Serenity's Glow
// ================================================

// Ubah kode payment_method di database jadi label yang enak dibaca
function paymentMethodLabel($method) {
    $labels = [
        'transfer_bank' => 'Transfer Bank',
        'e_wallet'      => 'E-Wallet',
        'cod'           => 'COD (Bayar di Tempat)',
    ];
    return $labels[$method] ?? ucfirst(str_replace('_', ' ', $method));
}
