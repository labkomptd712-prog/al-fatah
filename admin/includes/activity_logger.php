<?php
// admin/includes/activity_logger.php

if (!function_exists('logActivity')) {
    function logActivity($admin_id, $action_type, $module_name, $item_title, $description) {
        global $pdo;
        try {
            // Load database connection if not already loaded
            if (!isset($pdo)) {
                require_once __DIR__ . '/../config/db.php';
            }
            
            // Get username for log durability
            $stmt = $pdo->prepare("SELECT username FROM admins WHERE id = ?");
            $stmt->execute([(int)$admin_id]);
            $username = $stmt->fetchColumn() ?: 'Unknown';
            
            $ip = $_SERVER['REMOTE_ADDR'] ?? '';
            
            $stmtInsert = $pdo->prepare("INSERT INTO activity_logs (admin_id, admin_username, action_type, module_name, item_title, description, ip_address) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmtInsert->execute([
                (int)$admin_id,
                $username,
                $action_type,
                $module_name,
                $item_title,
                $description,
                $ip
            ]);
        } catch (Exception $e) {
            // Silently ignore log insertion failures so as not to interrupt main database queries
        }
    }
}
