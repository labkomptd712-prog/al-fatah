<?php
require_once __DIR__ . '/includes/public_init.php';
$page_title = 'Profil Sekolah';
$is_home = false;
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Profil Sekolah - SDIT AL FATAH</title>
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
          <h2>Profil Sekolah</h2>
          <ol>
            <li><a href="index.php">Beranda</a></li>
            <li>Profil Sekolah</li>
          </ol>
        </div>
      </div>
    </section>

    <section class="inner-page">
      <div class="container" data-aos="fade-up">
        <div class="section-title">
          <h2>Profil</h2>
          <p>Profil Sekolah SDIT Al Fatah</p>
        </div>
        <div style="color:#444; line-height:1.8; font-size:1.05rem;">
          <p><strong>[Tuliskan profil singkat sekolah di sini — sejarah singkat, keunggulan, dan gambaran umum SDIT Al Fatah]</strong></p>
          <p>[Paragraf placeholder: jelaskan identitas sekolah, lokasi, dan fokus pendidikan. Ganti teks ini dengan konten resmi dari pihak sekolah.]</p>
          <p>[Paragraf placeholder: tambahkan informasi keunggulan program, fasilitas, atau nilai-nilai yang ingin ditonjolkan.]</p>
          <p><em>Catatan: Halaman ini berisi teks contoh. Silakan ganti seluruh placeholder dengan profil sekolah yang sesuai.</em></p>
        </div>
      </div>
    </section>
  </main>

  <?php include __DIR__ . '/includes/public_footer.php'; ?>

  <?php include __DIR__ . '/includes/public_wa_float.php'; ?>
  <div id="preloader"></div>

  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/js/main.js"></script>
</body>
</html>
