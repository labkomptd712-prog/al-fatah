<?php
// admin/messages/list.php
require_once '../includes/auth.php';
require_role('admin'); // Hanya admin & superadmin
require_once '../config/db.php';

$message = $_GET['msg'] ?? '';
$error = $_GET['err'] ?? '';

// Detail pesan yang ingin dibuka (jika ada parameter view_id)
$view_id = intval($_GET['view_id'] ?? 0);
$selected_msg = null;

if ($view_id > 0) {
    try {
        // Tandai sebagai sudah dibaca
        $stmtUpdate = $pdo->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?");
        $stmtUpdate->execute([$view_id]);

        // Ambil detail pesan
        $stmtSelect = $pdo->prepare("SELECT * FROM contact_messages WHERE id = ?");
        $stmtSelect->execute([$view_id]);
        $selected_msg = $stmtSelect->fetch();
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Ambil seluruh daftar pesan masuk
try {
    $stmt = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
    $messages = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching messages: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan Masuk - SDIT Al Fatah</title>
    <link href="../../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="../../assets/css/admin.css" rel="stylesheet">
    <style>
        .unread-row {
            background-color: rgba(26, 204, 141, 0.04) !important;
            font-weight: 700 !important;
        }
        .unread-row td {
            color: #01036f !important;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include '../includes/sidebar.php'; ?>
        <main class="main-content">
            <div class="main-header d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold text-dark mb-1">Pesan Masuk (Inbox)</h4>
                    <p class="text-muted small mb-0">Pesan dari pengunjung halaman depan formulir Hubungi Kami</p>
                </div>
            </div>

            <?php if ($message === 'delete_success'): ?>
                <div class="alert alert-success alert-dismissible fade show"><i class="fa-solid fa-circle-check me-2"></i> Pesan berhasil dihapus!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endif; ?>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show"><i class="fa-solid fa-triangle-exclamation me-2"></i> <?= htmlspecialchars($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endif; ?>

            <div class="card border-0 rounded-4 shadow-sm overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4" style="width:50px;">No</th>
                                    <th>Pengirim</th>
                                    <th>Email</th>
                                    <th>Subjek</th>
                                    <th>Tanggal</th>
                                    <th class="pe-4 text-center" style="width:180px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($messages)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="fa-solid fa-envelope-open fa-3x mb-3 text-secondary opacity-25"></i>
                                            <p class="mb-0">Tidak ada pesan masuk.</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php $no = 1; foreach ($messages as $msg): 
                                        $is_unread = ((int)$msg['is_read'] === 0);
                                        $row_class = $is_unread ? 'unread-row' : '';
                                    ?>
                                        <tr class="<?= $row_class ?>">
                                            <td class="ps-4 text-secondary"><?= $no++ ?></td>
                                            <td class="fw-semibold"><?= htmlspecialchars($msg['name']) ?></td>
                                            <td><?= htmlspecialchars($msg['email']) ?></td>
                                            <td><?= htmlspecialchars($msg['subject']) ?></td>
                                            <td class="small text-secondary"><?= date('d M Y, H:i', strtotime($msg['created_at'])) ?></td>
                                            <td class="pe-4 text-center">
                                                <div class="d-inline-flex gap-2">
                                                    <a href="list.php?view_id=<?= (int) $msg['id'] ?>" class="btn btn-sm <?= $is_unread ? 'btn-success text-white' : 'btn-outline-primary' ?>" style="<?= $is_unread ? 'background-color: #1acc8d; border-color: #1acc8d;' : '' ?>" title="Baca Pesan"><i class="fa-solid fa-envelope-open-text me-1"></i> Baca</a>
                                                    <form action="delete.php" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesan ini?')">
                                                        <input type="hidden" name="id" value="<?= (int) $msg['id'] ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="fa-solid fa-trash-can"></i></button>
                                                    </form>
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

    <!-- Modal Detail Pesan -->
    <?php if ($selected_msg): ?>
    <div class="modal fade" id="messageDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow">
                <div class="modal-header bg-light border-0 py-3 rounded-top-4">
                    <h5 class="modal-title fw-bold text-dark"><i class="fa-solid fa-envelope text-success me-2" style="color: #1acc8d !important;"></i> Detail Pesan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="small text-secondary fw-semibold">Pengirim</label>
                        <p class="fw-bold text-dark mb-0"><?= htmlspecialchars($selected_msg['name']) ?></p>
                    </div>
                    <div class="mb-3">
                        <label class="small text-secondary fw-semibold">Email Pengirim</label>
                        <p class="text-dark mb-0"><a href="mailto:<?= htmlspecialchars($selected_msg['email']) ?>" class="text-decoration-none text-success"><?= htmlspecialchars($selected_msg['email']) ?></a></p>
                    </div>
                    <div class="mb-3">
                        <label class="small text-secondary fw-semibold">Subjek</label>
                        <p class="fw-bold text-dark mb-0"><?= htmlspecialchars($selected_msg['subject']) ?></p>
                    </div>
                    <div class="mb-3">
                        <label class="small text-secondary fw-semibold">Tanggal Kirim</label>
                        <p class="text-secondary small mb-0"><?= date('d F Y, H:i', strtotime($selected_msg['created_at'])) ?></p>
                    </div>
                    <hr class="my-3 border-light">
                    <div class="mb-0">
                        <label class="small text-secondary fw-semibold">Isi Pesan</label>
                        <div class="p-3 bg-light rounded-3 text-dark small" style="white-space: pre-wrap; line-height: 1.6; max-height: 200px; overflow-y: auto;">
                            <?= htmlspecialchars($selected_msg['message']) ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-brand py-2 px-4 rounded-3 text-white" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Bootstrap JS -->
    <script src="../../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <?php if ($selected_msg): ?>
    <script>
        // Otomatis tampilkan modal detail pesan saat halaman termuat
        document.addEventListener("DOMContentLoaded", function() {
            const modalEl = document.getElementById('messageDetailModal');
            if (modalEl) {
                const bsModal = new bootstrap.Modal(modalEl);
                bsModal.show();
                
                // Ketika modal ditutup, bersihkan query string view_id agar tidak memicu modal lagi jika direfresh
                modalEl.addEventListener('hidden.bs.modal', function () {
                    window.location.href = 'list.php';
                });
            }
        });
    </script>
    <?php endif; ?>
</body>
</html>
