<?php
require_once __DIR__ . '/includes/public_init.php';
$page_title = 'Fasilitas Sekolah';
$is_home = false;

try {
    // Ambil ID kategori 'Umum'
    $stmtUmum = $pdo->prepare("SELECT id FROM facility_categories WHERE slug = 'umum'");
    $stmtUmum->execute();
    $umum_id = $stmtUmum->fetchColumn();
    if (!$umum_id) {
        $umum_id = 0;
    }

    // Ambil semua fasilitas yang masuk kategori 'Umum'
    $stmtUmumFac = $pdo->prepare("SELECT * FROM facilities WHERE category_id = ? ORDER BY display_order ASC, name ASC");
    $stmtUmumFac->execute([$umum_id]);
    $umum_facilities = $stmtUmumFac->fetchAll();

    // Ambil kategori lainnya (selain 'Umum') beserta jumlah fasilitas di dalamnya
    $stmtOtherCats = $pdo->prepare("
        SELECT c.*, COUNT(f.id) as facility_count 
        FROM facility_categories c 
        LEFT JOIN facilities f ON c.id = f.category_id 
        WHERE c.slug != 'umum' 
        GROUP BY c.id 
        ORDER BY c.name ASC
    ");
    $stmtOtherCats->execute();
    $other_categories = $stmtOtherCats->fetchAll();
} catch (PDOException $e) {
    $umum_facilities = [];
    $other_categories = [];
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Fasilitas Sekolah - SDIT AL FATAH</title>
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
        cursor: pointer;
    }
    .folder-cover-wrapper {
        border: 2px solid #f8f9fa;
        background-color: #fcfcfc;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }
    .gallery-folder-card:hover .folder-cover-wrapper {
        border-color: #1acc8d;
        box-shadow: 0 10px 25px rgba(0,0,0,0.08) !important;
    }
    .gallery-folder-card:hover .folder-cover-wrapper img {
        transform: scale(1.06);
    }
    .gallery-folder-card:hover .fa-arrow-right {
        transform: translateX(5px);
    }
    .facility-static-card {
        background: #fff;
        border: 0;
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
          <h2>Fasilitas Sekolah</h2>
          <ol>
            <li><a href="index.php">Beranda</a></li>
            <li>Fasilitas</li>
          </ol>
        </div>
      </div>
    </section>

    <section class="inner-page">
      <div class="container" data-aos="fade-up">
        
        <!-- Bagian Kategori Umum -->
        <div class="section-title">
          <h2>Sarana Prasarana</h2>
          <p>Fasilitas Utama Sekolah</p>
        </div>

        <div class="row g-4 mb-5" data-aos="fade-left">
          <?php if (empty($umum_facilities)): ?>
            <div class="col-12 text-center py-5 text-muted">
                <i class="fa-regular fa-image fa-4x mb-3 text-secondary opacity-25"></i>
                <p>Belum ada sarana prasarana kategori Umum saat ini.</p>
            </div>
          <?php else: ?>
            <?php foreach ($umum_facilities as $fac): ?>
              <div class="col-lg-4 col-md-6">
                <!-- Clickable Card (Goes to Detail) -->
                <a href="fasilitas-detail.php?id=<?= (int)$fac['id'] ?>&back=umum" class="gallery-folder-card text-decoration-none d-block">
                  <div class="card rounded-4 shadow-sm h-100 overflow-hidden folder-cover-wrapper" style="border: 2px solid #f8f9fa; background-color: #fff; display: flex; flex-direction: column;">
                    
                    <div class="facility-img-container" style="height: 220px; overflow: hidden; position: relative; background: #f3f4f6;">
                      <?php if (!empty($fac['image']) && file_exists(__DIR__ . '/admin/uploads/' . $fac['image'])): ?>
                        <img src="admin/uploads/<?= htmlspecialchars($fac['image']) ?>" alt="<?= htmlspecialchars($fac['name']) ?>" class="w-100 h-100" style="object-fit: cover; transition: transform 0.5s ease;">
                      <?php else: ?>
                        <div class="d-flex flex-column align-items-center justify-content-center h-100 text-muted opacity-50">
                          <i class="fa-regular fa-image fa-3x mb-2"></i>
                          <span class="small">Foto belum tersedia</span>
                        </div>
                      <?php endif; ?>
                    </div>

                    <div class="card-body p-4 d-flex flex-column justify-content-between" style="flex-grow: 1;">
                      <div>
                        <h5 class="fw-bold text-dark mb-2" style="font-family: 'Poppins', sans-serif; font-size: 18px;"><?= htmlspecialchars($fac['name']) ?></h5>
                        <?php if (!empty($fac['description'])): ?>
                          <p class="text-secondary small mb-0" style="line-height: 1.6;"><?= nl2br(htmlspecialchars($fac['description'])) ?></p>
                        <?php else: ?>
                          <p class="text-muted small mb-0" style="font-style: italic; opacity: 0.6;">Sarana pendukung kegiatan belajar mengajar.</p>
                        <?php endif; ?>
                      </div>
                      
                      <!-- Interactive Arrow Indicator -->
                      <div class="text-end mt-3">
                        <i class="fa-solid fa-arrow-right" style="color: #1acc8d; font-size: 16px; transition: transform 0.3s ease;"></i>
                      </div>
                    </div>

                  </div>
                </a>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

        <!-- Bagian Kategori Lainnya -->
        <?php if (!empty($other_categories)): ?>
          <div class="section-title mt-5 pt-4" data-aos="fade-up">
            <h2>Kategori Lainnya</h2>
            <p>Fasilitas Tambahan</p>
          </div>

          <div class="row g-4" data-aos="fade-up" data-aos-delay="100">
            <?php foreach ($other_categories as $cat): ?>
              <?php 
              $cover_src = '';
              if (!empty($cat['cover_image'])) {
                  $cover_src = 'admin/uploads/' . $cat['cover_image'];
              } else {
                  $stmtFirst = $pdo->prepare("SELECT image FROM facilities WHERE category_id = ? AND image IS NOT NULL AND image != '' ORDER BY display_order ASC, created_at DESC LIMIT 1");
                  $stmtFirst->execute([$cat['id']]);
                  $first_photo = $stmtFirst->fetchColumn();
                  if ($first_photo) {
                      $cover_src = 'admin/uploads/' . $first_photo;
                  }
              }
              ?>
              <div class="col-lg-4 col-md-6">
                <!-- Clickable Folder Card -->
                <a href="fasilitas-kategori.php?slug=<?= htmlspecialchars($cat['slug']) ?>" class="gallery-folder-card text-decoration-none">
                  <div class="folder-cover-wrapper position-relative overflow-hidden rounded-4 shadow-sm" style="height: 200px;">
                    <?php if (!empty($cover_src) && file_exists(__DIR__ . '/' . $cover_src)): ?>
                      <img src="<?= htmlspecialchars($cover_src) ?>" alt="<?= htmlspecialchars($cat['name']) ?>" class="img-fluid w-100 h-100" style="object-fit: cover; transition: transform 0.5s ease;">
                    <?php else: ?>
                      <div class="d-flex flex-column align-items-center justify-content-center h-100 text-muted">
                        <i class="fa-regular fa-image fa-4x text-success mb-2 opacity-50" style="color: #1acc8d !important;"></i>
                        <span class="small text-secondary">Kategori Kosong</span>
                      </div>
                    <?php endif; ?>
                    <div class="photo-count-badge position-absolute top-0 end-0 bg-dark bg-opacity-75 text-white px-3 py-1.5 rounded-bl-4 small" style="border-bottom-left-radius: 12px; font-weight: 600;">
                      <i class="fa-solid fa-hotel me-1"></i> <?= (int)$cat['facility_count'] ?> Item
                    </div>
                  </div>
                  <div class="p-3 text-center position-relative">
                    <h5 class="fw-bold text-dark mb-1" style="font-family: 'Poppins', sans-serif; font-size: 18px;"><?= htmlspecialchars($cat['name']) ?></h5>
                    <span class="text-secondary small">Klik untuk membuka folder</span>
                    <i class="fa-solid fa-arrow-right position-absolute" style="bottom: 15px; right: 15px; color: #1acc8d; font-size: 16px; transition: transform 0.3s ease;"></i>
                  </div>
                </a>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

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
