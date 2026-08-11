<?php
require_once __DIR__ . '/includes/public_init.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: fasilitas.php");
    exit();
}

try {
    // Ambil info detail fasilitas
    $stmtFac = $pdo->prepare("SELECT f.*, c.name as category_name, c.slug as category_slug FROM facilities f LEFT JOIN facility_categories c ON f.category_id = c.id WHERE f.id = ?");
    $stmtFac->execute([$id]);
    $facility = $stmtFac->fetch();
    
    if (!$facility) {
        header("Location: fasilitas.php");
        exit();
    }
    
    // Ambil seluruh foto dalam galeri fasilitas ini
    $stmtPhotos = $pdo->prepare("SELECT * FROM facility_photos WHERE facility_id = ? ORDER BY urutan ASC, created_at DESC");
    $stmtPhotos->execute([$id]);
    $photos = $stmtPhotos->fetchAll();
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

$page_title = 'Detail Fasilitas - ' . $facility['name'];
$is_home = false;

// Tautan Kembali Kontekstual
$back_param = trim($_GET['back'] ?? '');
if (empty($back_param) || $back_param === 'umum') {
    $back_url = 'fasilitas.php';
    $back_label = 'Fasilitas';
} else {
    $back_url = 'fasilitas-kategori.php?slug=' . urlencode($back_param);
    $back_label = $facility['category_name'];
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title><?= htmlspecialchars($facility['name']) ?> - SDIT AL FATAH</title>
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
    .gallery-item {
        overflow: hidden;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .gallery-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    .gallery-item img {
        transition: transform 0.5s ease;
    }
    .gallery-item:hover img {
        transform: scale(1.06);
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
          <h2>Detail Fasilitas: <?= htmlspecialchars($facility['name']) ?></h2>
          <ol>
            <li><a href="index.php">Beranda</a></li>
            <li><a href="fasilitas.php">Fasilitas</a></li>
            <?php if ($back_label !== 'Fasilitas'): ?>
              <li><a href="<?= $back_url ?>"><?= htmlspecialchars($back_label) ?></a></li>
            <?php endif; ?>
            <li><?= htmlspecialchars($facility['name']) ?></li>
          </ol>
        </div>
      </div>
    </section>

    <!-- ======= Details Section ======= -->
    <section id="gallery" class="gallery inner-page">
      <div class="container" data-aos="fade-up">
        
        <!-- Header Info Fasilitas -->
        <div class="row mb-5 justify-content-center">
          <div class="col-lg-8 text-center">
            <h3 class="fw-bold text-dark mb-3" style="font-family: 'Poppins', sans-serif;"><?= htmlspecialchars($facility['name']) ?></h3>
            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1.5 mb-3 small" style="font-size: 13px;">
              Kategori: <?= htmlspecialchars($facility['name'] === 'Umum' || $facility['category_slug'] === 'umum' ? 'Fasilitas Utama' : $facility['category_name']) ?>
            </span>
            <p class="text-secondary leading-relaxed mt-2" style="font-size: 15px;">
              <?= !empty($facility['description']) ? nl2br(htmlspecialchars($facility['description'])) : 'Sarana pendukung kegiatan belajar mengajar di lingkungan sekolah SDIT Al Fatah.' ?>
            </p>
            <div class="mt-4">
              <a href="<?= $back_url ?>" class="btn btn-brand btn-sm px-4 py-2 rounded-pill"><i class="fa-solid fa-arrow-left me-1"></i> Kembali ke <?= htmlspecialchars($back_label) ?></a>
            </div>
          </div>
        </div>

        <!-- Grid Album Multi-Foto -->
        <div class="row g-4 justify-content-center" data-aos="fade-left">
          <?php if (empty($photos)): ?>
            <!-- Fallback Jika Album Kosong -->
            <div class="col-lg-6 text-center py-5">
              <div class="bg-light rounded-4 p-5 text-muted border border-dashed">
                <i class="fa-regular fa-image fa-4x mb-3 text-success opacity-25" style="color: #1acc8d !important;"></i>
                <h6 class="fw-bold text-dark mb-1">Album foto belum tersedia</h6>
                <p class="small mb-0">Admin sekolah belum mengunggah foto detail pendukung untuk sarana prasarana ini.</p>
              </div>
            </div>
          <?php else: ?>
            <?php foreach ($photos as $index => $p): ?>
              <?php 
              $img_path = 'admin/uploads/' . $p['photo_path'];
              $alt = 'Foto Detail ' . $facility['name'] . ' - ' . ($index + 1);
              ?>
              <div class="col-lg-4 col-md-6">
                <div class="gallery-item" data-aos="zoom-in" data-aos-delay="<?= $index * 50 ?>">
                  <!-- GLightbox Class Integration -->
                  <a href="<?= htmlspecialchars($img_path) ?>" class="gallery-lightbox" title="<?= htmlspecialchars($alt) ?>">
                    <img src="<?= htmlspecialchars($img_path) ?>" alt="<?= htmlspecialchars($alt) ?>" class="img-fluid w-100" style="height: 250px; object-fit: cover;">
                  </a>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
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
