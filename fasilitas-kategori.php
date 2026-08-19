<?php
require_once __DIR__ . '/includes/public_init.php';

$slug = trim($_GET['slug'] ?? '');
if ($slug === '') {
    header("Location: fasilitas.php");
    exit();
}

try {
    // Ambil detail kategori
    $stmtCat = $pdo->prepare("SELECT * FROM facility_categories WHERE slug = ?");
    $stmtCat->execute([$slug]);
    $category = $stmtCat->fetch();
    
    if (!$category) {
        header("Location: fasilitas.php");
        exit();
    }
    
    // Ambil seluruh fasilitas dalam kategori ini
    $stmtFac = $pdo->prepare("SELECT * FROM facilities WHERE category_id = ? ORDER BY display_order ASC, name ASC");
    $stmtFac->execute([$category['id']]);
    $facilities = $stmtFac->fetchAll();
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

$page_title = 'Fasilitas ' . $category['name'];
$is_home = false;
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Fasilitas <?= htmlspecialchars($category['name']) ?> - SDIT AL FATAH</title>
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
        cursor: pointer;
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
          <h2>Kategori: <?= htmlspecialchars($category['name']) ?></h2>
          <ol>
            <li><a href="index.php">Beranda</a></li>
            <li><a href="fasilitas.php">Fasilitas</a></li>
            <li><?= htmlspecialchars($category['name']) ?></li>
          </ol>
        </div>
      </div>
    </section>

    <!-- ======= Facilities Grid Section ======= -->
    <section id="gallery" class="gallery inner-page">
      <div class="container" data-aos="fade-up">
        <div class="section-title">
          <h2>Daftar Sarana</h2>
          <p><?= htmlspecialchars($category['name']) ?></p>
        </div>

        <div class="row g-4" data-aos="fade-left">
          <?php if (empty($facilities)): ?>
            <div class="col-12 text-center py-5 text-muted">
                <i class="fa-regular fa-image fa-4x mb-3 text-secondary opacity-25"></i>
                <p>Belum ada sarana prasarana dalam kategori ini.</p>
                <a href="fasilitas.php" class="btn btn-brand btn-sm mt-3"><i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Fasilitas</a>
            </div>
          <?php else: ?>
            <?php foreach ($facilities as $fac): ?>
              <div class="col-lg-4 col-md-6">
                <!-- Clickable Card (Goes to Detail) -->
                <a href="fasilitas-detail.php?id=<?= (int)$fac['id'] ?>&back=<?= urlencode($slug) ?>" class="gallery-folder-card text-decoration-none d-block">
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
