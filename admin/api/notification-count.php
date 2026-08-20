<?php
// admin/api/notification-count.php
require_once '../includes/auth.php';
require_admin_role(); // Memastikan hanya admin dan superadmin yang dapat mengakses

require_once '../config/db.php';

header('Content-Type: application/json');

$unread_messages = 0;
$pending_news = 0;
$pending_revisi = 0;

try {
    $unread_messages = (int) $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0")->fetchColumn();
    $pending_news = (int) $pdo->query("SELECT COUNT(*) FROM news WHERE status = 'pending'")->fetchColumn();
    $pending_revisi = (int) $pdo->query("SELECT COUNT(*) FROM revision_requests WHERE status = 'pending'")->fetchColumn();
} catch (PDOException $e) {
    // Fail silently
}

echo json_encode([
    'unread_messages' => $unread_messages,
    'pending_news' => $pending_news,
    'pending_revisi' => $pending_revisi
]);
exit();
