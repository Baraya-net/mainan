<?php

require_once __DIR__ . '/vendor/autoload.php';

// --- KONFIGURASI PENTING ---
// Pastikan konfigurasi ini SAMA PERSIS dengan di file CreateInvoice.php
$merchantKey = "32d50d1ffd04213b5435877c65d6fd0e"; // Diambil dari screenshot Anda
$merchantCode = "DS25268"; // Diambil dari screenshot Anda
// --------------------------

$duitkuConfig = new \Duitku\Config($merchantKey, $merchantCode);
$duitkuConfig->setSandboxMode(true); // true untuk sandbox, false untuk produksi
// $duitkuConfig->setDuitkuLogs(false); // Nonaktifkan log jika tidak diperlukan


// Duitku akan mengirim notifikasi via HTTP POST
try {
    // Validasi callback dan dapatkan data notifikasi
    $callback = \Duitku\Pop::callback($duitkuConfig);

    // Ubah data JSON menjadi object
    $notif = json_decode($callback);

    if ($notif->resultCode == "00") {
        // === PROSES BISNIS ANDA DI SINI ===
        // 1. Cek `merchantOrderId` di database Anda.
        // 2. Pastikan transaksi tersebut belum pernah diproses sebelumnya.
        // 3. Update status pesanan di database Anda menjadi "LUNAS" atau "BERHASIL".
        // 4. Kirim email konfirmasi ke pelanggan.
        // ===================================
        
        // Beri respons ke Duitku bahwa notifikasi sudah diterima
        http_response_code(200);
        echo "OK";


    } else {
        // === PROSES JIKA GAGAL ===
        // Update status pesanan di database Anda menjadi "GAGAL".
        // =========================

        // Beri respons bahwa notifikasi diterima meskipun gagal
        http_response_code(200);
        echo "OK";
    }

} catch (Exception $e) {
    // Tangani jika ada error (misalnya: signature tidak valid)
    http_response_code(400); // Bad Request
    echo $e->getMessage();
}

