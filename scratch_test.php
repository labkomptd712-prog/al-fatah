<?php
require_once __DIR__ . '/admin/config/db.php';
try {
    // Tampilkan data sebelum dibersihkan
    $stmt = $pdo->query("SELECT id, name, description FROM facilities");
    $facs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "=== SEBELUM DIBERSIHKAN ===\n";
    foreach ($facs as $f) {
        echo "ID: {$f['id']} | Name: {$f['name']} | Description: " . substr(str_replace("\n", " ", $f['description']), 0, 100) . "\n";
    }

    // Bersihkan data yang terkena korup warning PHP
    $stmtClean = $pdo->query("UPDATE facilities SET description = NULL WHERE description LIKE '%Deprecated%' OR description LIKE '%Warning%' OR description LIKE '%Error%'");
    echo "\nBaris dibersihkan: " . $stmtClean->rowCount() . "\n";

    // Tampilkan data setelah dibersihkan
    $stmt = $pdo->query("SELECT id, name, description FROM facilities");
    $facs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "\n=== SETELAH DIBERSIHKAN ===\n";
    foreach ($facs as $f) {
        echo "ID: {$f['id']} | Name: {$f['name']} | Description: " . substr(str_replace("\n", " ", $f['description']), 0, 100) . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
