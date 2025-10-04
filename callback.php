<?php

// Memuat semua pustaka dari folder vendor yang dibuat oleh Composer
require_once __DIR__ . '/vendor/autoload.php';

// PENAMBAHAN: Memastikan request ini datang dari metode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Metode request harus POST.']);
    exit; // Hentikan eksekusi
}


// --- KONFIGURASI PENTING ---
// Ganti dengan Merchant Key dan Merchant Code Anda
// Anda bisa dapatkan dari dashboard Duitku
$merchantKey = "732B39FC61796845775D2C4FB05332AF"; // Ganti dengan Merchant Key Anda
$merchantCode = "D0001"; // Ganti dengan Merchant Code Anda
// --------------------------

$duitkuConfig = new \Duitku\Config($merchantKey, $merchantCode);

// Atur ke 'false' untuk mode produksi (transaksi sungguhan)
// Atur ke 'true' untuk mode sandbox (percobaan)
$duitkuConfig->setSandboxMode(true);

// Atur ke 'false' jika Anda tidak ingin Duitku membuat file log
// $duitkuConfig->setDuitkuLogs(false);


// Ambil data dari AJAX request di CreateInvoice.html
// Pastikan untuk membersihkan input ini di aplikasi production Anda
$paymentAmount      = isset($_POST['paymentAmount']) ? (int)$_POST['paymentAmount'] : 0;
$productDetail      = isset($_POST['productDetail']) ? $_POST['productDetail'] : 'Test Payment';
$email              = isset($_POST['email']) ? $_POST['email'] : 'customer@gmail.com';
$phoneNumber        = isset($_POST['phoneNumber']) ? $_POST['phoneNumber'] : '081234567890';
$paymentMethod      = isset($_POST['paymentMethod']) ? $_POST['paymentMethod'] : ''; // Opsional

$merchantOrderId    = time(); // ID pesanan unik dari sisi Anda
$additionalParam    = ''; // Opsional
$merchantUserInfo   = ''; // Opsional
$customerVaName     = 'John Doe'; // Nama yang akan muncul di halaman pembayaran

// --- URL PENTING ---
// URL ini harus bisa diakses secara publik oleh server Duitku
// Untuk pengujian lokal, gunakan Ngrok atau sejenisnya
$callbackUrl        = 'http://YOUR_SERVER/example/composer/Callback.php'; 
$returnUrl          = 'http://YOUR_SERVER/example/composer/CreateInvoice.html'; 
// -------------------

$expiryPeriod       = 60; // Masa berlaku invoice dalam menit

// Detail Pelanggan (Opsional)
$firstName          = "John";
$lastName           = "Doe";

$address = array(
    'firstName'     => $firstName,
    'lastName'      => $lastName,
    'address'       => "Jl. Kembangan Raya",
    'city'          => "Jakarta",
    'postalCode'    => "11530",
    'phone'         => $phoneNumber,
    'countryCode'   => "ID"
);

$customerDetail = array(
    'firstName'         => $firstName,
    'lastName'          => $lastName,
    'email'             => $email,
    'phoneNumber'       => $phoneNumber,
    'billingAddress'    => $address,
    'shippingAddress'   => $address
);


// Detail Item (Opsional)
$item1 = array(
    'name'      => $productDetail,
    'price'     => $paymentAmount,
    'quantity'  => 1
);

$itemDetails = array(
    $item1
);

// Kumpulkan semua parameter
$params = array(
    'paymentAmount'     => $paymentAmount,
    'merchantOrderId'   => $merchantOrderId,
    'productDetails'    => $productDetail,
    'additionalParam'   => $additionalParam,
    'merchantUserInfo'  => $merchantUserInfo,
    'customerVaName'    => $customerVaName,
    'email'             => $email,
    'phoneNumber'       => $phoneNumber,
    'itemDetails'       => $itemDetails,
    'customerDetail'    => $customerDetail,
    'callbackUrl'       => $callbackUrl,
    'returnUrl'         => $returnUrl,
    'expiryPeriod'      => $expiryPeriod
);

// Jika ada metode pembayaran spesifik yang dipilih
if (!empty($paymentMethod)) {
    $params['paymentMethod'] = $paymentMethod;
}


try {
    // Meminta pembuatan invoice ke Duitku
    $responseDuitkuPop = \Duitku\Pop::createInvoice($params, $duitkuConfig);

    header('Content-Type: application/json');
    echo $responseDuitkuPop;
} catch (Exception $e) {
    // Tangani error jika terjadi
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

