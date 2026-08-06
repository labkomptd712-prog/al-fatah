<?php
/**
 * Bootstrap publik: koneksi DB + settings (key-value).
 * Pakai admin/config/db.php — jangan buat koneksi terpisah.
 */
require_once __DIR__ . '/../admin/config/db.php';

$settings = [];
try {
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
    $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];
} catch (PDOException $e) {
    $settings = [];
}

$setting_keys = [
    'visi', 'misi', 'qa_list',
    'wa_number', 'wa_message',
    'ig_link', 'fb_link', 'yt_link', 'tiktok_link',
];
foreach ($setting_keys as $key) {
    if (!isset($settings[$key])) {
        $settings[$key] = '';
    }
}

/**
 * URL WhatsApp dari settings (wa_number + wa_message).
 */
function alfatah_wa_url(array $settings): string
{
    $num = preg_replace('/[^0-9]/', '', $settings['wa_number'] ?? '');
    if ($num === '') {
        return '#';
    }
    $msg = trim($settings['wa_message'] ?? '');
    $url = 'https://wa.me/' . $num;
    if ($msg !== '') {
        $url .= '?text=' . rawurlencode($msg);
    }
    return $url;
}
