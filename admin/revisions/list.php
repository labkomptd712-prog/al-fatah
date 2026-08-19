<?php
// admin/revisions/list.php
require_once '../includes/auth.php';
require_once '../config/db.php';

// Memastikan hanya Admin/Superadmin yang bisa mengakses
if (!is_admin_role()) {
    header("Location: ../dashboard.php?err=" . urlencode("Akses ditolak."));
    exit();
}

$status_filter = $_GET['status'] ?? 'pending';
if (!in_array($status_filter, ['all', 'pending', 'selesai'], true)) {
    $status_filter = 'pending';
}

$message = $_GET['msg'] ?? '';
$error = $_GET['err'] ?? '';

try {
    $query = "SELECT r.*, a.username as requested_by_name FROM revision_requests r JOIN admins a ON r.requested_by = a.id";
    if ($status_filter === 'pending') {
        $query .= " WHERE r.status = 'pending'";
    } elseif ($status_filter === 'selesai') {
        $query .= " WHERE r.status = 'selesai'";
    }
    $query .= " ORDER BY r.created_at DESC";
    
    $stmt = $pdo->query($query);
    $revisions = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching revisions: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Catatan Revisi - SDIT Al Fatah</title>
    <!-- Bootstrap 5 CSS -->
    <link href="../../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Custom Admin CSS -->
    <link href="../../assets/css/admin.css" rel="stylesheet">
</head>
<body>
    <div class="admin-container">
        <?php include '../includes/sidebar.php'; ?>
        
        <main class="main-content">
            <header class="main-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
                <div>
                    <h4 class="fw-bold text-dark mb-1"><i class="fa-solid fa-comments text-success me-2" style="color: #1acc8d !important;"></i> Kelola Catatan Revisi</h4>
                    <p class="text-muted small mb-0">Daftar semua permintaan revisi konten yang diajukan oleh Kepala Sekolah.</p>
                </div>
            </header>

            <?php if (!empty($message)): ?>
                <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i> <?= htmlspecialchars($message) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
                    <i class="fa-solid fa-circle-exclamation me-2"></i> <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Filter Status -->
            <div class="card border-0 rounded-4 shadow-sm mb-4">
                <div class="card-body p-3 d-flex flex-wrap gap-2">
                    <a href="?status=pending" class="btn btn-sm <?= $status_filter === 'pending' ? 'btn-success text-white' : 'btn-outline-secondary' ?> py-1.5 px-3 rounded-2" style="<?= $status_filter === 'pending' ? 'background: #1acc8d; border-color: #1acc8d;' : '' ?>">
                        <i class="fa-solid fa-clock me-1"></i> Pending
                    </a>
                    <a href="?status=selesai" class="btn btn-sm <?= $status_filter === 'selesai' ? 'btn-success text-white' : 'btn-outline-secondary' ?> py-1.5 px-3 rounded-2" style="<?= $status_filter === 'selesai' ? 'background: #1acc8d; border-color: #1acc8d;' : '' ?>">
                        <i class="fa-solid fa-circle-check me-1"></i> Selesai
                    </a>
                    <a href="?status=all" class="btn btn-sm <?= $status_filter === 'all' ? 'btn-success text-white' : 'btn-outline-secondary' ?> py-1.5 px-3 rounded-2" style="<?= $status_filter === 'all' ? 'background: #1acc8d; border-color: #1acc8d;' : '' ?>">
                        <i class="fa-solid fa-list me-1"></i> Semua
                    </a>
                </div>
            </div>

            <!-- List Revisions Table -->
            <div class="card border-0 rounded-4 shadow-sm overflow-hidden mb-5">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="min-width: 800px;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4" style="width: 200px;">Judul Item</th>
                                    <th style="width: 150px;">Modul</th>
                                    <th>Catatan Revisi</th>
                                    <th style="width: 120px;">Diajukan Oleh</th>
                                    <th style="width: 150px;">Tanggal Pengajuan</th>
                                    <th style="width: 120px;">Status</th>
                                    <th class="pe-4 text-center" style="width: 180px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($revisions)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <i class="fa-regular fa-comment-dots fa-3x mb-3 text-secondary opacity-25"></i>
                                            <p class="mb-0">Tidak ada data catatan revisi.</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($revisions as $rev): 
                                        $edit_link = '';
                                        if ($rev['module_name'] === 'berita') $edit_link = '../news/edit.php?id=' . $rev['item_id'];
                                        elseif ($rev['module_name'] === 'fasilitas') $edit_link = '../facilities/edit.php?id=' . $rev['item_id'];
                                        elseif ($rev['module_name'] === 'ekskul') $edit_link = '../ekskul/edit.php?id=' . $rev['item_id'];
                                        elseif ($rev['module_name'] === 'prestasi') $edit_link = '../prestasi/edit.php?id=' . $rev['item_id'];
                                        elseif ($rev['module_name'] === 'testimonial') $edit_link = '../testimonials/edit.php?id=' . $rev['item_id'];
                                        elseif ($rev['module_name'] === 'team') $edit_link = '../team/edit.php?id=' . $rev['item_id'];
                                        elseif ($rev['module_name'] === 'kelas') $edit_link = '../classes/edit.php?id=' . $rev['item_id'];
                                        elseif ($rev['module_name'] === 'struktur') $edit_link = '../struktur/edit.php?id=' . $rev['item_id'];
                                        elseif ($rev['module_name'] === 'galeri') $edit_link = '../gallery/list.php';
                                    ?>
                                        <tr>
                                            <td class="ps-4 fw-bold text-dark small"><?= htmlspecialchars($rev['item_title']) ?></td>
                                            <td><span class="badge bg-info bg-opacity-10 text-info px-2 py-1 small"><?= htmlspecialchars(ucfirst($rev['module_name'])) ?></span></td>
                                            <td class="small text-muted" style="white-space: pre-wrap;"><?= htmlspecialchars($rev['catatan']) ?></td>
                                            <td><span class="badge bg-secondary small"><?= htmlspecialchars($rev['requested_by_name']) ?></span></td>
                                            <td class="text-secondary small" style="font-size: 11px;">
                                                <?= date('d M Y, H:i', strtotime($rev['created_at'])) ?>
                                            </td>
                                            <td>
                                                <?php if ($rev['status'] === 'pending'): ?>
                                                    <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-1.5"><i class="fa-solid fa-clock me-1 small"></i> Pending</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1.5"><i class="fa-solid fa-circle-check me-1 small"></i> Selesai</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="pe-4 text-center">
                                                <div class="d-inline-flex gap-2">
                                                    <?php if ($edit_link && $rev['status'] === 'pending'): ?>
                                                        <a href="<?= $edit_link ?>" class="btn btn-sm btn-outline-primary py-1 px-2.5 rounded-2" style="font-size: 11px;"><i class="fa-solid fa-pen-to-square"></i> Lihat/Edit</a>
                                                    <?php endif; ?>
                                                    
                                                    <?php if ($rev['status'] === 'pending'): ?>
                                                        <form action="resolve.php" method="POST" class="d-inline" onsubmit="return confirm('Tandai revisi ini sebagai selesai?')">
                                                            <input type="hidden" name="id" value="<?= (int) $rev['id'] ?>">
                                                            <input type="hidden" name="redirect" value="list">
                                                            <button type="submit" class="btn btn-sm btn-success py-1 px-2.5 rounded-2 text-white" style="background-color: #1acc8d; border-color: #1acc8d; font-size: 11px;"><i class="fa-solid fa-check"></i> Selesai</button>
                                                        </form>
                                                    <?php else: ?>
                                                        <span class="text-muted small" style="font-size: 11px;"><i class="fa-solid fa-calendar-check me-1"></i> <?= date('d/m/y', strtotime($rev['resolved_at'])) ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="../../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
