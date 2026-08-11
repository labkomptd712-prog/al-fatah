<?php
// admin/settings.php
require_once 'includes/auth.php';
require_admin_role();
require_once 'config/db.php';

$expected_keys = [
    'visi', 
    'misi', 
    'qa_list', 
    'phone_number',
    'wa_number', 
    'wa_message', 
    'ig_link', 
    'fb_link', 
    'yt_link', 
    'tiktok_link',
    'stats_siswa',
    'stats_guru',
    'stats_tendik',
    'stats_sarpras'
];

$error = '';
$message = '';

// Handling Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        // Prepare Upsert Statement (Insert or Update on duplicate key)
        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");

        foreach ($expected_keys as $key) {
            $value = $_POST[$key] ?? '';
            // Trim whitespace, but preserve newlines in textarea inputs
            if ($key === 'misi' || $key === 'qa_list') {
                $value = trim($value);
            } else {
                $value = trim($value);
            }
            $stmt->execute([$key, $value, $value]);
        }

        $pdo->commit();
        $message = 'settings_updated';
    } catch (PDOException $e) {
        $pdo->rollBack();
        $error = "Gagal memperbarui pengaturan: " . $e->getMessage();
    }
}

// Load current settings from database
try {
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
    $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    // Ensure all expected keys exist in the array to prevent PHP undefined index warnings
    foreach ($expected_keys as $key) {
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
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Website - SDIT Al Fatah</title>
    <!-- Bootstrap 5 CSS -->
    <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Custom Admin CSS -->
    <link href="../assets/css/admin.css" rel="stylesheet">
</head>
<body>

    <div class="admin-container">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <div class="main-header d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold text-dark mb-1">Pengaturan Profil & Media</h4>
                    <p class="text-muted small mb-0">Kelola visi, misi, kontak, dan tautan media sosial sekolah</p>
                </div>
            </div>

            <!-- Notifications -->
            <?php if ($message === 'settings_updated'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i> Pengaturan berhasil diperbarui!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i> <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="row g-4">
                    <!-- Left Column: School Profile -->
                    <div class="col-lg-7">
                        <div class="card border-0 rounded-4 shadow-sm p-4 h-100">
                            <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-school text-success me-2"></i> Profil Sekolah</h5>
                            <hr class="mt-0 mb-3 border-light">

                            <div class="mb-3">
                                <label for="visi" class="form-label fw-semibold text-secondary">Visi Sekolah</label>
                                <textarea class="form-control bg-light border-0 p-3 rounded-3" id="visi" name="visi" rows="3" placeholder="Masukkan visi sekolah..." required><?= htmlspecialchars($settings['visi']) ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="misi" class="form-label fw-semibold text-secondary">Misi Sekolah</label>
                                <textarea class="form-control bg-light border-0 p-3 rounded-3" id="misi" name="misi" rows="6" placeholder="Masukkan misi sekolah (tulis satu misi per baris)..." required><?= htmlspecialchars($settings['misi']) ?></textarea>
                                <small class="text-muted">Tuliskan setiap butir misi pada <strong>baris baru</strong> agar terformat menjadi daftar di halaman depan.</small>
                            </div>

                            <div class="mb-0">
                                <label for="qa_list" class="form-label fw-semibold text-secondary">12 Quality Assurance (Jaminan Mutu)</label>
                                <textarea class="form-control bg-light border-0 p-3 rounded-3" id="qa_list" name="qa_list" rows="6" placeholder="Masukkan jaminan mutu lulusan (tulis satu per baris)..." required><?= htmlspecialchars($settings['qa_list']) ?></textarea>
                                <small class="text-muted">Tuliskan setiap butir jaminan mutu pada <strong>baris baru</strong>.</small>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Contacts and Social Links -->
                    <div class="col-lg-5">
                        <div class="d-flex flex-column gap-4">
                            <!-- Kontak & WhatsApp -->
                            <div class="card border-0 rounded-4 shadow-sm p-4">
                                <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-phone text-success me-2"></i> Kontak Sekolah</h5>
                                <hr class="mt-0 mb-3 border-light">

                                <div class="mb-3">
                                    <label for="phone_number" class="form-label fw-semibold text-secondary">Nomor Telepon (Footer)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0 text-muted"><i class="fa-solid fa-phone"></i></span>
                                        <input type="text" class="form-control bg-light border-0 py-2.5 rounded-end-3" id="phone_number" name="phone_number" value="<?= htmlspecialchars($settings['phone_number']) ?>" placeholder="Contoh: 6282122229862" required>
                                    </div>
                                    <small class="text-muted">Digunakan untuk tampilan teks kontak di footer halaman publik.</small>
                                </div>

                                <div class="mb-3">
                                    <label for="wa_number" class="form-label fw-semibold text-secondary">Nomor WhatsApp (Tombol Floating)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0 text-muted"><i class="fa-brands fa-whatsapp text-success"></i></span>
                                        <input type="text" class="form-control bg-light border-0 py-2.5 rounded-end-3" id="wa_number" name="wa_number" value="<?= htmlspecialchars($settings['wa_number']) ?>" placeholder="Contoh: 628123456789" required>
                                    </div>
                                    <small class="text-muted">Gunakan kode negara di awal (misal: <strong>62</strong>812...). Tanpa spasi/tanda hubung.</small>
                                </div>

                                <div class="mb-0">
                                    <label for="wa_message" class="form-label fw-semibold text-secondary">Template Pesan Chat WhatsApp</label>
                                    <textarea class="form-control bg-light border-0 p-3 rounded-3" id="wa_message" name="wa_message" rows="2" placeholder="Pesan otomatis ketika calon wali murid mengklik link WA..."><?= htmlspecialchars($settings['wa_message']) ?></textarea>
                                </div>
                            </div>

                            <!-- Statistik Sekolah -->
                            <div class="card border-0 rounded-4 shadow-sm p-4">
                                <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-chart-simple text-success me-2"></i> Statistik Sekolah</h5>
                                <hr class="mt-0 mb-3 border-light">

                                <div class="row g-2">
                                    <div class="col-6 mb-3">
                                        <label for="stats_siswa" class="form-label fw-semibold text-secondary">Peserta Didik</label>
                                        <input type="number" class="form-control bg-light border-0 py-2 rounded-3 text-secondary" id="stats_siswa" name="stats_siswa" value="<?= htmlspecialchars($settings['stats_siswa']) ?>" required min="0">
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label for="stats_guru" class="form-label fw-semibold text-secondary">Pendidik (Guru)</label>
                                        <input type="number" class="form-control bg-light border-0 py-2 rounded-3 text-secondary" id="stats_guru" name="stats_guru" value="<?= htmlspecialchars($settings['stats_guru']) ?>" required min="0">
                                    </div>
                                    <div class="col-6 mb-0">
                                        <label for="stats_tendik" class="form-label fw-semibold text-secondary">Tendik (Staf)</label>
                                        <input type="number" class="form-control bg-light border-0 py-2 rounded-3 text-secondary" id="stats_tendik" name="stats_tendik" value="<?= htmlspecialchars($settings['stats_tendik']) ?>" required min="0">
                                    </div>
                                    <div class="col-6 mb-0">
                                        <label for="stats_sarpras" class="form-label fw-semibold text-secondary">Ruang Sarpras</label>
                                        <input type="number" class="form-control bg-light border-0 py-2 rounded-3 text-secondary" id="stats_sarpras" name="stats_sarpras" value="<?= htmlspecialchars($settings['stats_sarpras']) ?>" required min="0">
                                    </div>
                                </div>
                            </div>

                            <!-- Social Links -->
                            <div class="card border-0 rounded-4 shadow-sm p-4">
                                <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-share-nodes text-success me-2"></i> Tautan Media Sosial</h5>
                                <hr class="mt-0 mb-3 border-light">

                                <div class="mb-3">
                                    <label for="ig_link" class="form-label fw-semibold text-secondary"><i class="fa-brands fa-instagram text-danger me-1"></i> Link Instagram</label>
                                    <input type="url" class="form-control bg-light border-0 py-2 rounded-3 text-secondary" id="ig_link" name="ig_link" value="<?= htmlspecialchars($settings['ig_link']) ?>" placeholder="https://instagram.com/akun_sekolah">
                                </div>

                                <div class="mb-3">
                                    <label for="fb_link" class="form-label fw-semibold text-secondary"><i class="fa-brands fa-facebook text-primary me-1"></i> Link Facebook</label>
                                    <input type="url" class="form-control bg-light border-0 py-2 rounded-3 text-secondary" id="fb_link" name="fb_link" value="<?= htmlspecialchars($settings['fb_link']) ?>" placeholder="https://facebook.com/akun_sekolah">
                                </div>

                                <div class="mb-3">
                                    <label for="yt_link" class="form-label fw-semibold text-secondary"><i class="fa-brands fa-youtube text-danger me-1"></i> Link YouTube</label>
                                    <input type="url" class="form-control bg-light border-0 py-2 rounded-3 text-secondary" id="yt_link" name="yt_link" value="<?= htmlspecialchars($settings['yt_link']) ?>" placeholder="https://youtube.com/c/saluran_sekolah">
                                </div>

                                <div class="mb-0">
                                    <label for="tiktok_link" class="form-label fw-semibold text-secondary"><i class="fa-brands fa-tiktok text-dark me-1"></i> Link TikTok</label>
                                    <input type="url" class="form-control bg-light border-0 py-2 rounded-3 text-secondary" id="tiktok_link" name="tiktok_link" value="<?= htmlspecialchars($settings['tiktok_link']) ?>" placeholder="https://tiktok.com/@akun_sekolah">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-brand w-100 py-3 rounded-3 fw-bold"><i class="fa-solid fa-floppy-disk me-2"></i> Simpan Semua Pengaturan</button>
                        </div>
                    </div>
                </div>
            </form>
        </main>
    </div>

    <!-- Bootstrap JS -->
    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
