<?php
// admin/activity-log/list.php
require_once '../includes/auth.php';
require_admin_role(); // Pastikan hanya admin & superadmin yang memiliki akses

require_once '../config/db.php';

$filter_action = $_GET['action_type'] ?? '';
$filter_admin = $_GET['admin_username'] ?? '';
$filter_date = $_GET['date'] ?? '';

$where = [];
$params = [];

if ($filter_action !== '') {
    $where[] = "action_type = ?";
    $params[] = $filter_action;
}

if ($filter_admin !== '') {
    $where[] = "admin_username = ?";
    $params[] = $filter_admin;
}

if ($filter_date !== '') {
    $where[] = "DATE(created_at) = ?";
    $params[] = $filter_date;
}

$where_clause = '';
if (!empty($where)) {
    $where_clause = "WHERE " . implode(" AND ", $where);
}

// Pagination
$limit = 20;
$page = intval($_GET['page'] ?? 1);
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

try {
    // Count total
    $count_query = "SELECT COUNT(*) FROM activity_logs $where_clause";
    $stmtCount = $pdo->prepare($count_query);
    $stmtCount->execute($params);
    $total_records = (int) $stmtCount->fetchColumn();
    $total_pages = ceil($total_records / $limit);

    // Fetch logs
    $logs_query = "SELECT * FROM activity_logs $where_clause ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
    $stmtLogs = $pdo->prepare($logs_query);
    $stmtLogs->execute($params);
    $logs = $stmtLogs->fetchAll();

    // Get list of admins for dropdown
    $admins = $pdo->query("SELECT DISTINCT admin_username FROM activity_logs ORDER BY admin_username ASC")->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    die("Error fetching logs: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Aktivitas - SDIT Al Fatah</title>
    <link href="../../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="../../assets/css/admin.css" rel="stylesheet">
    <style>
        .badge-create {
            background-color: rgba(25, 135, 84, 0.15) !important;
            color: #198754 !important;
            font-weight: 600;
        }
        .badge-update {
            background-color: rgba(255, 193, 7, 0.15) !important;
            color: #ffc107 !important;
            font-weight: 600;
        }
        .badge-delete {
            background-color: rgba(220, 53, 69, 0.15) !important;
            color: #dc3545 !important;
            font-weight: 600;
        }
        .badge-login {
            background-color: rgba(13, 202, 240, 0.15) !important;
            color: #0dcaf0 !important;
            font-weight: 600;
        }
        /* Mobile responsive card list helper */
        @media (max-width: 767.98px) {
            .filter-card {
                padding: 1rem !important;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include '../includes/sidebar.php'; ?>
        <main class="main-content">
            <div class="main-header d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold text-dark mb-1">Riwayat Aktivitas Admin</h4>
                    <p class="text-muted small mb-0">Log audit riwayat penambahan, pengeditan, penghapusan data, dan login admin</p>
                </div>
            </div>

            <!-- Panel Filter -->
            <div class="card border-0 rounded-4 shadow-sm mb-4 filter-card">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3 col-sm-6">
                            <label for="admin_username" class="form-label small fw-semibold">Admin / Pengguna</label>
                            <select name="admin_username" id="admin_username" class="form-select bg-light border-0 py-2 px-3 rounded-3">
                                <option value="">-- Semua Admin --</option>
                                <?php foreach ($admins as $admName): ?>
                                    <option value="<?= htmlspecialchars($admName) ?>" <?= $filter_admin === $admName ? 'selected' : '' ?>><?= htmlspecialchars($admName) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <label for="action_type" class="form-label small fw-semibold">Aksi</label>
                            <select name="action_type" id="action_type" class="form-select bg-light border-0 py-2 px-3 rounded-3">
                                <option value="">-- Semua Aksi --</option>
                                <option value="create" <?= $filter_action === 'create' ? 'selected' : '' ?>>Create (Tambah)</option>
                                <option value="update" <?= $filter_action === 'update' ? 'selected' : '' ?>>Update (Ubah)</option>
                                <option value="delete" <?= $filter_action === 'delete' ? 'selected' : '' ?>>Delete (Hapus)</option>
                                <option value="login" <?= $filter_action === 'login' ? 'selected' : '' ?>>Login</option>
                            </select>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <label for="date" class="form-label small fw-semibold">Tanggal</label>
                            <input type="date" name="date" id="date" class="form-control bg-light border-0 py-2 px-3 rounded-3" value="<?= htmlspecialchars($filter_date) ?>">
                        </div>
                        <div class="col-md-3 col-sm-6 d-flex align-items-end">
                            <div class="d-flex w-100 gap-2">
                                <button type="submit" class="btn btn-brand py-2 px-3 flex-fill text-white"><i class="fa-solid fa-filter me-1"></i> Filter</button>
                                <a href="list.php" class="btn btn-outline-secondary py-2 px-3"><i class="fa-solid fa-rotate-left"></i></a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabel Data -->
            <div class="card border-0 rounded-4 shadow-sm overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4" style="width: 180px;">Waktu</th>
                                    <th style="width: 150px;">Admin</th>
                                    <th style="width: 100px;">Aksi</th>
                                    <th style="width: 150px;">Modul</th>
                                    <th>Detail Aktivitas</th>
                                    <th class="pe-4" style="width: 130px;">IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($logs)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="fa-solid fa-clock-rotate-left fa-3x mb-3 text-secondary opacity-25"></i>
                                            <p class="mb-0">Tidak ada riwayat aktivitas yang ditemukan.</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($logs as $log): 
                                        $badge_class = '';
                                        $action_label = '';
                                        switch ($log['action_type']) {
                                            case 'create':
                                                $badge_class = 'badge-create';
                                                $action_label = 'Create';
                                                break;
                                            case 'update':
                                                $badge_class = 'badge-update';
                                                $action_label = 'Update';
                                                break;
                                            case 'delete':
                                                $badge_class = 'badge-delete';
                                                $action_label = 'Delete';
                                                break;
                                            case 'login':
                                                $badge_class = 'badge-login';
                                                $action_label = 'Login';
                                                break;
                                        }
                                    ?>
                                        <tr>
                                            <td class="ps-4 text-secondary small"><?= date('d M Y, H:i:s', strtotime($log['created_at'])) ?></td>
                                            <td class="fw-semibold text-dark"><?= htmlspecialchars($log['admin_username']) ?></td>
                                            <td>
                                                <span class="badge <?= $badge_class ?> px-2.5 py-1.5 rounded-pill text-uppercase" style="font-size: 10px;"><?= $action_label ?></span>
                                            </td>
                                            <td class="text-secondary small fw-semibold text-capitalize"><?= htmlspecialchars($log['module_name']) ?></td>
                                            <td class="small text-dark text-wrap" style="max-width: 400px;"><?= htmlspecialchars($log['description']) ?></td>
                                            <td class="pe-4 text-secondary small"><?= htmlspecialchars($log['ip_address'] ?: '-') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <nav class="d-flex justify-content-center mt-4">
                    <ul class="pagination pagination-sm gap-1">
                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link rounded-3 border-0 py-2 px-3" href="?page=<?= $page - 1 ?>&admin_username=<?= urlencode($filter_admin) ?>&action_type=<?= urlencode($filter_action) ?>&date=<?= urlencode($filter_date) ?>"><i class="fa-solid fa-chevron-left"></i></a>
                        </li>
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= $page === $i ? 'active' : '' ?>">
                                <a class="page-link rounded-3 border-0 py-2 px-3 <?= $page === $i ? 'bg-brand text-white' : '' ?>" href="?page=<?= $i ?>&admin_username=<?= urlencode($filter_admin) ?>&action_type=<?= urlencode($filter_action) ?>&date=<?= urlencode($filter_date) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                            <a class="page-link rounded-3 border-0 py-2 px-3" href="?page=<?= $page + 1 ?>&admin_username=<?= urlencode($filter_admin) ?>&action_type=<?= urlencode($filter_action) ?>&date=<?= urlencode($filter_date) ?>"><i class="fa-solid fa-chevron-right"></i></a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </main>
    </div>

    <!-- Bootstrap JS -->
    <script src="../../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
