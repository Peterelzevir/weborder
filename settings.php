<?php
/**
 * Konfigurasi global untuk sistem login Telegram
 * File ini disisipkan di semua file frontend
 */
// Pastikan semua timestamp menggunakan UTC timezone
date_default_timezone_set('UTC');

// Konfigurasi URL backend Flask
$url = "https://kreasiunik.my.id/bot"; // Ganti dengan URL server Flask Anda

// Konfigurasi OTP - PENTING: Sesuaikan dengan backend (5 menit)
$otp_expiration = 300; // 5 menit (dalam detik) - disesuaikan dengan backend

// Konfigurasi Password 2FA
$password_expiration = 300; // 5 menit (dalam detik)

// Konfigurasi keamanan
$max_otp_attempts = 3;
$max_password_attempts = 3;

// Force session berjalan di HTTP Secure
ini_set('session.cookie_secure', 1);

// Konfigurasi session
// Set waktu penyimpanan sesi
ini_set('session.gc_maxlifetime', 3600); // 1 jam
session_set_cookie_params(3600); // Cookie berlaku 1 jam

// Debug mode - set ke false untuk produksi
$debug_mode = true;

// Fungsi untuk mencatat log ke file jika debug mode aktif
function debug_log($message) {
    global $debug_mode;
    if ($debug_mode) {
        $log_file = __DIR__ . '/logs/debug_' . date('Y-m-d') . '.log';
        
        // Buat direktori logs jika belum ada
        if (!is_dir(dirname($log_file))) {
            mkdir(dirname($log_file), 0755, true);
        }
        
        // Format log dengan timestamp UTC
        $log_message = '[' . gmdate('Y-m-d H:i:s') . ' UTC] ' . $message . PHP_EOL;
        
        // Tulis ke file log
        file_put_contents($log_file, $log_message, FILE_APPEND);
    }
}

// Catat informasi server saat startup
debug_log('==== SESSION START ====');
debug_log('PHP Server Time (UTC): ' . gmdate('Y-m-d H:i:s'));
debug_log('Session ID: ' . session_id());

// Daftar kode negara
$country_codes = [
    // Asian Countries
    'AE' => '+971',  // United Arab Emirates
];

/**
 * Fungsi untuk validasi dan format nomor telepon internasional
 * 
 * @param string $phoneNumber Nomor telepon yang akan diformat
 * @param string $countryCode Kode negara dengan format +XX (contoh: +62, +1, +44)
 * @return string Nomor telepon terformat dengan kode negara
 */
function validateAndFormatPhone($phoneNumber, $countryCode = '+62') {
    // Hapus karakter non-digit kecuali tanda +
    $cleaned = preg_replace('/[^\d+]/', '', $phoneNumber);
    
    // Jika sudah diawali dengan +, kembalikan sebagai-is
    if (strpos($cleaned, '+') === 0) {
        return $cleaned;
    }
    
    // Ambil kode negara tanpa tanda +
    $countryDigits = substr($countryCode, 1);
    
    // Jika diawali dengan kode negara tanpa +
    if (strpos($cleaned, $countryDigits) === 0) {
        return '+' . $cleaned;
    }
    
    // Jika diawali dengan 0, ganti 0 dengan kode negara
    if (strpos($cleaned, '0') === 0) {
        return $countryCode . substr($cleaned, 1);
    }
    
    // Default, tambahkan kode negara
    return $countryCode . $cleaned;
}

/**
 * Fungsi untuk validasi nomor telepon internasional
 * 
 * @param string $phoneNumber Nomor telepon yang akan divalidasi
 * @return bool TRUE jika format valid, FALSE jika tidak
 */
function isValidInternationalPhone($phoneNumber) {
    // Format dasar nomor internasional: + diikuti kode negara dan minimal 7 digit
    $pattern = '/^\+[1-9]\d{1,4}\d{6,14}$/';
    
    // Periksa panjang minimum (kode negara + minimal 7 digit)
    if (strlen($phoneNumber) < 8) {
        return false;
    }
    
    return preg_match($pattern, $phoneNumber) === 1;
}