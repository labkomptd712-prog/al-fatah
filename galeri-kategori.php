<?php
require_once __DIR__ . '/includes/public_init.php';

$slug = trim($_GET['slug'] ?? '');
if ($slug === '') {
    header("Location: galeri.php");
    exit();
}

try {
    // Ambil detail kategori
    $stmtCat = $pdo->prepare("SELECT * FROM gallery_categories WHERE slug = ?");
    $stmtCat->execute([$slug]);
    $category = $stmtCat->fetch();
    
    if (!$category) {
        header("Location: galeri.php");
        exit();
    }
    
    // Ambil seluruh foto dalam kategori ini
    $stmtPhotos = $pdo->prepare("SELECT * FROM gallery WHERE category_id = ? ORDER BY created_at DESC");
    $stmtPhotos->execute([$category['id']]);
    $photos = $stmtPhotos->fetchAll();
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

$page_title = 'Galeri ' . $category['name'];
$is_home = false;
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Galeri <?= htmlspecialchars($category['name']) ?> - SDIT AL FATAH</title>
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
            <li><a href="galeri.php">Galeri Foto</a></li>
            <li><?= htmlspecialchars($category['name']) ?></li>
          </ol>
        </div>
      </div>
    </section>

    <!-- ======= Gallery Section ======= -->
    <section id="gallery" class="gallery inner-page">
      <div class="container" data-aos="fade-up">
        <div class="section-title">
          <h2>Koleksi Foto</h2>
          <p><?= htmlspecialchars($category['name']) ?></p>
        </div>

        <div class="row g-0" data-aos="fade-left">
          <?php if (empty($photos)): ?>
            <div class="col-12 text-center py-5 text-muted">
                <i class="fa-regular fa-images fa-4x mb-3 text-secondary opacity-25"></i>
                <p>Belum ada foto dalam kategori ini.</p>
                <a href="galeri.php" class="btn btn-brand btn-sm mt-3"><i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Galeri</a>
            </div>
          <?php else: ?>
            <?php
            $delay = 100;
            foreach ($photos as $photo):
                $img_path = 'admin/uploads/' . $photo['image'];
                $alt = ($photo['caption'] !== '' && $photo['caption'] !== null)
                  ? $photo['caption']
                  : 'Galeri SDIT Al Fatah - ' . $category['name'];
            ?>
              <div class="col-lg-3 col-md-4">
                <div class="gallery-item" data-aos="zoom-in" data-aos-delay="<?= (int) $delay ?>">
                  <a href="<?= htmlspecialchars($img_path) ?>" class="gallery-lightbox" title="<?= htmlspecialchars($alt) ?>">
                    <img src="<?= htmlspecialchars($img_path) ?>" alt="<?= htmlspecialchars($alt) ?>" class="img-fluid" style="width:100%; height:200px; object-fit:cover;">
                  </a>
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
