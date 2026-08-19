<?php
require_once __DIR__ . '/includes/public_init.php';

$slug = trim($_GET['slug'] ?? '');
if ($slug === '') {
    header("Location: prestasi.php");
    exit();
}

try {
    // Ambil detail kategori
    $stmtCat = $pdo->prepare("SELECT * FROM prestasi_categories WHERE slug = ?");
    $stmtCat->execute([$slug]);
    $category = $stmtCat->fetch();
    
    if (!$category) {
        header("Location: prestasi.php");
        exit();
    }
    
    // Ambil seluruh prestasi dalam kategori ini
    $stmtPhotos = $pdo->prepare("SELECT * FROM prestasi WHERE category_id = ? ORDER BY created_at DESC");
    $stmtPhotos->execute([$category['id']]);
    $photos = $stmtPhotos->fetchAll();
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

$page_title = 'Prestasi ' . $category['name'];
$is_home = false;
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Prestasi <?= htmlspecialchars($category['name']) ?> - SDIT AL FATAH</title>
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
    .prestasi-card {
      transition: all 0.3s ease;
    }
    .prestasi-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0,0,0,0.08) !important;
      border-color: #1acc8d !important;
    }
    .prestasi-card:hover img {
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
          <h2>Kategori Prestasi: <?= htmlspecialchars($category['name']) ?></h2>
          <ol>
            <li><a href="index.php">Beranda</a></li>
            <li><a href="prestasi.php">Prestasi</a></li>
            <li><?= htmlspecialchars($category['name']) ?></li>
          </ol>
        </div>
      </div>
    </section>

    <!-- ======= Prestasi Section ======= -->
    <section id="gallery" class="gallery inner-page">
      <div class="container" data-aos="fade-up">
        <div class="section-title">
          <h2>Koleksi Prestasi</h2>
          <p><?= htmlspecialchars($category['name']) ?></p>
        </div>

        <div class="row g-4" data-aos="fade-left">
          <?php if (empty($photos)): ?>
            <div class="col-12 text-center py-5 text-muted">
                <i class="fa-solid fa-trophy fa-4x mb-3 text-secondary opacity-25"></i>
                <p>Belum ada prestasi dalam kategori ini.</p>
                <a href="prestasi.php" class="btn btn-brand btn-sm mt-3"><i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Prestasi</a>
            </div>
          <?php else: ?>
            <?php
            $delay = 100;
            foreach ($photos as $photo):
                $img_path = 'admin/uploads/' . $photo['foto'];
                
                // Lightbox caption
                $lightbox_title = $photo['nama_siswa'] . ' - ' . $photo['jenis_lomba'];
                if (!empty($photo['keterangan'])) {
                    $lightbox_title .= ' (' . $photo['keterangan'] . ')';
                }
            ?>
              <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="prestasi-card rounded-4 overflow-hidden bg-white shadow-sm border border-light h-100 d-flex flex-column" data-aos="zoom-in" data-aos-delay="<?= (int)$delay ?>">
                  <div class="prestasi-img-wrapper" style="height: 200px; overflow: hidden; position: relative;">
                    <a href="<?= htmlspecialchars($img_path) ?>" class="gallery-lightbox" title="<?= htmlspecialchars($lightbox_title) ?>">
                      <img src="<?= htmlspecialchars($img_path) ?>" alt="<?= htmlspecialchars($photo['nama_siswa']) ?>" class="w-100 h-100" style="object-fit: cover; transition: transform 0.3s ease;">
                    </a>
                  </div>
                  <div class="p-3 text-center d-flex flex-column flex-grow-1 justify-content-center">
                    <h5 class="fw-bold text-dark mb-1" style="font-size: 15px;"><?= htmlspecialchars($photo['nama_siswa']) ?></h5>
                    <span class="text-secondary small d-block"><?= htmlspecialchars($photo['jenis_lomba']) ?></span>
                    <?php if (!empty($photo['keterangan'])): ?>
                      <small class="text-muted mt-2 d-block" style="font-size: 12px; font-style: italic;"><?= htmlspecialchars($photo['keterangan']) ?></small>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            <?php
                $delay += 50;
            endforeach;
            ?>
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
