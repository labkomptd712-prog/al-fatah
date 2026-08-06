<?php
require_once __DIR__ . '/includes/public_init.php';
$page_title = 'Struktur Organisasi';
$is_home = false;

$struktur = [
    ['role' => 'Kepala Sekolah', 'name' => '[Nama Kepala Sekolah]'],
    ['role' => 'Wakil Kepala Sekolah', 'name' => '[Nama Wakil Kepala Sekolah]'],
    ['role' => 'Kepala Tata Usaha', 'name' => '[Nama Kepala Tata Usaha]'],
    ['role' => 'Koordinator Kurikulum', 'name' => '[Nama Koordinator Kurikulum]'],
    ['role' => 'Koordinator Kesiswaan', 'name' => '[Nama Koordinator Kesiswaan]'],
    ['role' => 'Koordinator Sarpras', 'name' => '[Nama Koordinator Sarpras]'],
    ['role' => 'Wali Kelas 1A', 'name' => '[Nama Wali Kelas 1A]'],
    ['role' => 'Wali Kelas 1B', 'name' => '[Nama Wali Kelas 1B]'],
    ['role' => 'Wali Kelas 2A', 'name' => '[Nama Wali Kelas 2A]'],
    ['role' => 'Wali Kelas 2B', 'name' => '[Nama Wali Kelas 2B]'],
    ['role' => 'Wali Kelas 3A', 'name' => '[Nama Wali Kelas 3A]'],
    ['role' => 'Wali Kelas 3B', 'name' => '[Nama Wali Kelas 3B]'],
    ['role' => 'Wali Kelas 4A', 'name' => '[Nama Wali Kelas 4A]'],
    ['role' => 'Wali Kelas 4B', 'name' => '[Nama Wali Kelas 4B]'],
    ['role' => 'Wali Kelas 5A', 'name' => '[Nama Wali Kelas 5A]'],
    ['role' => 'Wali Kelas 5B', 'name' => '[Nama Wali Kelas 5B]'],
    ['role' => 'Wali Kelas 6A', 'name' => '[Nama Wali Kelas 6A]'],
    ['role' => 'Wali Kelas 6B', 'name' => '[Nama Wali Kelas 6B]'],
];
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Struktur Organisasi - SDIT AL FATAH</title>
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
          <h2>Struktur Organisasi</h2>
          <ol>
            <li><a href="index.php">Beranda</a></li>
            <li>Struktur Organisasi</li>
          </ol>
        </div>
      </div>
    </section>

    <section class="inner-page">
      <div class="container" data-aos="fade-up">
        <div class="section-title">
          <h2>Profil</h2>
          <p>Struktur Organisasi</p>
        </div>
        <p class="mb-4 text-muted"><em>Ganti setiap placeholder [Nama ...] dengan nama pejabat/staf yang sebenarnya.</em></p>

        <div class="row g-4 struktur-grid">
          <?php foreach ($struktur as $i => $slot): ?>
          <div class="col-lg-3 col-md-4 col-sm-6" data-aos="zoom-in" data-aos-delay="<?= min(100 + ($i * 50), 400) ?>">
            <div class="struktur-slot">
              <div class="struktur-role"><?= htmlspecialchars($slot['role']) ?></div>
              <p class="struktur-name"><?= htmlspecialchars($slot['name']) ?></p>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
  </main>

  <footer id="footer">
    <div class="footer-top">
      <div class="container">
        <div class="row">
          <div class="col-lg-4 col-md-6">
            <div class="footer-info">
              <h3>SDIT AL FATAH</h3>
              <p class="pb-3"><em>"Bersama Mencetak Generasi Islami yang Cerdas dan Berakhlak Mulia"</em></p>
              <p>
                Jl. Masjid Al-Muawanah No.60, RT.006/RW.012, Aren Jaya, Kec. Bekasi Tim., Kota Bks, Jawa Barat 17111 <br>
                <br><br>
                <strong>Phone:</strong> +62 0000 0000 0000<br>
                <strong>Email:</strong> sditalfatah.60@gmail.com<br>
              </p>
              <?php include __DIR__ . '/includes/public_footer_social.php'; ?>
            </div>
          </div>
          <div class="col-lg-2 col-md-6 footer-links">
            <h4>Layanan Kepegawaian</h4>
            <ul>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Administrasi Umum</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Surat Keluar Masuk</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Surat PPDB</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Administrasi Penilaian</a></li>
            </ul>
          </div>
          <div class="col-lg-2 col-md-6 footer-links">
            <h4>Tautan</h4>
            <ul>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Profil Dapodik</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Periksa NISN</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#">informasi KJP</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#">PPDB</a></li>
            </ul>
          </div>
          <div class="col-lg-4 col-md-6 footer-newsletter">
            <h4>Our Newsletter</h4>
            <p>Subscribe channel Youtube kami</p>
            <form action="" method="post">
              <input type="email" name="email"><input type="submit" value="Subscribe">
            </form>
          </div>
        </div>
      </div>
    </div>
    <div class="container">
      <div class="copyright">
        &copy; Copyright <strong><span style="color: #0fff63;">Sdit Al Fatah</span></strong>. All Rights Reserved
      </div>
      <div class="credits">
        Designed by <Strong><span style="color: #00ff59;">Adriansyah</span></Strong>
      </div>
    </div>
  </footer>

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
