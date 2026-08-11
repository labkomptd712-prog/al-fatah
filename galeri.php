<?php
require_once __DIR__ . '/includes/public_init.php';
$page_title = 'Galeri Kategori';
$is_home = false;

try {
    $stmt = $pdo->query("SELECT c.*, COUNT(g.id) as photo_count FROM gallery_categories c LEFT JOIN gallery g ON c.id = g.category_id GROUP BY c.id ORDER BY c.name ASC");
    $categories = $stmt->fetchAll();
} catch (PDOException $e) {
    $categories = [];
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Galeri Kategori - SDIT AL FATAH</title>
  <link href="assets/img/logo afix.png" rel="icon">
  <link href="assets/img/logo afix.png" rel="apple-touch-icon">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Montserrat:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
  <style>
    .gallery-folder-card {
        display: block;
        transition: all 0.3s ease;
    }
    .gallery-folder-card:hover {
        transform: translateY(-5px);
    }
    .folder-cover-wrapper {
        border: 2px solid #f8f9fa;
        background-color: #fcfcfc;
    }
    .gallery-folder-card:hover .folder-cover-wrapper {
        border-color: #1acc8d;
        box-shadow: 0 10px 25px rgba(0,0,0,0.08) !important;
    }
  </style>
</head>

<body>

  <header id="header" class="fixed-top d-flex align-items-center">
    <div class="container d-flex align-items-center justify-content-between">
      <div class="logo">
        <a href="index.php"><img src="assets/img/logo afix.png" alt="" class="img-fluid"></a>
        <h1><a href="index.php"><span><I>SDIT AL FATAH</I></span></a></h1>
      </div>
      <?php include __DIR__ . '/includes/public_nav.php'; ?>
    </div>
  </header>

  <main id="main">
    <section class="breadcrumbs">
      <div class="container">
        <div class="d-flex justify-content-between align-items-center">
          <h2>Galeri Foto</h2>
          <ol>
            <li><a href="index.php">Beranda</a></li>
            <li>Galeri Foto</li>
          </ol>
        </div>
      </div>
    </section>

    <section class="inner-page">
      <div class="container" data-aos="fade-up">
        <div class="section-title">
          <h2>Koleksi</h2>
          <p>Kategori Galeri Sekolah</p>
        </div>

        <div class="row g-4">
          <?php 
          if (empty($categories)): 
          ?>
            <div class="col-12 text-center py-5 text-muted">
                <i class="fa-regular fa-folder-open fa-4x mb-3 text-secondary opacity-25"></i>
                <p>Belum ada kategori galeri yang tersedia saat ini.</p>
            </div>
          <?php 
          else:
            foreach ($categories as $cat): 
                // Cari cover image: custom cover atau fallback foto pertama di kategori
                $cover_src = '';
                if (!empty($cat['cover_image'])) {
                    $cover_src = 'admin/uploads/' . $cat['cover_image'];
                } else {
                    $stmtFirst = $pdo->prepare("SELECT image FROM gallery WHERE category_id = ? ORDER BY created_at DESC LIMIT 1");
                    $stmtFirst->execute([$cat['id']]);
                    $firstPhoto = $stmtFirst->fetchColumn();
                    if ($firstPhoto) {
                        $cover_src = 'admin/uploads/' . $firstPhoto;
                    }
                }
          ?>
            <div class="col-lg-4 col-md-6" data-aos="zoom-in">
                <a href="galeri-kategori.php?slug=<?= htmlspecialchars($cat['slug']) ?>" class="gallery-folder-card text-decoration-none">
                    <div class="folder-cover-wrapper position-relative overflow-hidden rounded-4 shadow-sm bg-light" style="height: 200px;">
                        <?php if (!empty($cover_src)): ?>
                            <img src="<?= htmlspecialchars($cover_src) ?>" alt="<?= htmlspecialchars($cat['name']) ?>" class="img-fluid w-100 h-100" style="object-fit: cover;">
                        <?php else: ?>
                            <div class="d-flex flex-column align-items-center justify-content-center h-100 text-muted">
                                <i class="fa-regular fa-image fa-4x text-success mb-2 opacity-50" style="color: #1acc8d !important;"></i>
                                <span class="small text-secondary">Kategori Kosong</span>
                            </div>
                        <?php endif; ?>
                        <div class="photo-count-badge position-absolute top-0 end-0 bg-dark bg-opacity-75 text-white px-3 py-1.5 rounded-bl-4 small" style="border-bottom-left-radius: 12px; font-weight: 600;">
                            <i class="fa-solid fa-images me-1"></i> <?= (int)$cat['photo_count'] ?>
                        </div>
                    </div>
                    <div class="p-3 text-center position-relative">
                        <h5 class="fw-bold text-dark mb-1"><?= htmlspecialchars($cat['name']) ?></h5>
                        <span class="text-secondary small">Klik untuk membuka folder</span>
                        <i class="fa-solid fa-arrow-right position-absolute" style="bottom: 15px; right: 15px; color: #1acc8d; font-size: 16px;"></i>
                    </div>
                </a>
            </div>
          <?php 
            endforeach; 
          endif; 
          ?>
        </div>

      </div>
    </section>
  </main>

  <?php include __DIR__ . '/includes/public_footer.php'; ?>

  <?php include __DIR__ . '/includes/public_wa_float.php'; ?>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/js/main.js"></script>

</body>
</html>
