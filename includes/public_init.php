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
    'ig_link', 'fb_link', 'yt_link', 'tiktok_link', 'phone_number',
    'stats_siswa', 'stats_guru', 'stats_tendik', 'stats_sarpras'
];
foreach ($setting_keys as $key) {
    if (!isset($settings[$key]) || $settings[$key] === '') {
        if ($key === 'stats_siswa') {
            $settings[$key] = '605';
        } elseif ($key === 'stats_guru') {
            $settings[$key] = '33';
        } elseif ($key === 'stats_tendik') {
            $settings[$key] = '3';
        } elseif ($key === 'stats_sarpras') {
            $settings[$key] = '24';
        } else {
            $settings[$key] = '';
        }
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
        $url .= '?text=' . urlencode($msg);
    }
    return $url;
}

/**
 * Format nomor telepon untuk tampilan publik (e.g. 6282122229862 -> 0821 2222 9862)
 */
function format_phone_display(?string $phone): string
{
    $num = preg_replace('/[^0-9]/', '', $phone ?? '');
    if ($num === '') {
        return '';
    }
    if (substr($num, 0, 2) === '62') {
        $num = '0' . substr($num, 2);
    }
    return preg_replace('/(\d{4})(\d{4})(\d+)/', '$1 $2 $3', $num);
}

// Visitor Tracking with Admin/Editor exclusion
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin_id'])) {
    try {
        $visitor_ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $page_url = $_SERVER['REQUEST_URI'] ?? '/';
        $stmtVisit = $pdo->prepare("INSERT INTO page_visits (page_url, visitor_ip) VALUES (?, ?)");
        $stmtVisit->execute([$page_url, $visitor_ip]);
    } catch (PDOException $e) {
        // Ignore errors silently
    }
}

// Output buffering untuk memformat otomatis semua tulisan "SDIT Al Fatah" menggunakan Arial Bold Italic
ob_start(function($buffer) {
    $parts = preg_split('/(<[^>]+>)/', $buffer, -1, PREG_SPLIT_DELIM_CAPTURE);
    $in_exclude_tag = false;
    $exclude_tags = ['title', 'script', 'style', 'textarea'];
    
    foreach ($parts as &$part) {
        if (empty($part)) continue;
        if ($part[0] === '<') {
            $tag_name = strtolower(preg_replace('/^<\/?([a-z1-6]+).*/i', '$1', $part));
            if (in_array($tag_name, $exclude_tags)) {
                if (strpos($part, '</') === 0) {
                    $in_exclude_tag = false;
                } else {
                    $in_exclude_tag = true;
                }
            }
        } else {
            if (!$in_exclude_tag) {
                // Gunakan regex untuk membungkus variasi teks SDIT Al Fatah
                $part = preg_replace(
                    '/(SDIT\s+Al[ -]Fatah|SDIT\s+AL\s+FATAH|Sdit\s+Al\s+Fatah)/i', 
                    '<span class="brand-font">$1</span>', 
                    $part
                );
            }
        }
    }
    return implode('', $parts);
});

