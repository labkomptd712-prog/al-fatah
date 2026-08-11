<?php
require_once __DIR__ . '/includes/public_init.php';

$visi = trim($settings['visi'] ?? '');
$misi_lines = array_filter(array_map('trim', explode("\n", $settings['misi'] ?? '')));
$qa_lines = array_filter(array_map('trim', explode("\n", $settings['qa_list'] ?? '')));

$page_title = 'Visi & Misi';
$is_home = false;
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Visi &amp; Misi - SDIT AL FATAH</title>
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
    .premium-list-card {
        background: #fafbfc;
        border-left: 4px solid #1acc8d;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease, background-color 0.3s ease;
        padding: 1.25rem 1.5rem !important;
    }
    .premium-list-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(26, 204, 141, 0.16) !important;
        background: #fff;
    }
    /* 1. Misi Sekolah Overrides */
    .premium-list-card .text-dark.leading-relaxed {
        font-size: 15.5px !important;
        font-weight: 500 !important;
    }
    .premium-list-card .bg-success.bg-opacity-10 {
        min-width: 40px !important;
        height: 40px !important;
        background-color: rgba(26, 204, 141, 0.12) !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        box-shadow: 0 3px 8px rgba(26, 204, 141, 0.2);
    }
    .premium-list-card .bi-patch-check-fill {
        font-size: 20px !important;
        color: #1acc8d !important;
    }
    /* 2. 12 Quality Assurance Overrides */
    .qa-number {
        font-family: 'Poppins', sans-serif;
        font-size: 13.5px !important;
        font-weight: 700 !important;
        color: #fff !important;
        background: linear-gradient(135deg, #1acc8d, #0f9f6e) !important;
        width: 36px !important;
        height: 36px !important;
        min-width: 36px !important;
        max-width: 36px !important;
        border-radius: 50% !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        opacity: 1 !important;
        box-shadow: 0 3px 8px rgba(26, 204, 141, 0.25);
    }
    .premium-list-card .text-dark.fw-semibold {
        font-size: 15.5px !important;
        font-weight: 600 !important;
        color: #1a2536 !important;
        margin-left: 8px;
    }
    /* Spacing Row Gap */
    .inner-page .row.g-3 {
        row-gap: 1.5rem !important;
    }
    /* 3. Heading Section Decoratives */
    .inner-page .section-title h2 {
        color: #1acc8d !important;
        font-weight: 600 !important;
    }
    .inner-page .section-title h2::after {
        height: 3px !important;
        background: linear-gradient(90deg, #1acc8d, #010483) !important;
        width: 90px !important;
        margin: 4px 12px !important;
    }
    .inner-page .section-title p {
        position: relative;
        padding-bottom: 12px;
        display: inline-block;
    }
    .inner-page .section-title p::after {
        content: "";
        position: absolute;
        left: 0;
        bottom: 0;
        width: 60px;
        height: 4px;
        background: linear-gradient(90deg, #1acc8d, #010483);
        border-radius: 2px;
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
          <h2>Visi &amp; Misi</h2>
          <ol>
            <li><a href="index.php">Beranda</a></li>
            <li>Visi &amp; Misi</li>
          </ol>
        </div>
      </div>
    </section>

    <section class="inner-page">
      <div class="container" data-aos="fade-up">

        <div class="section-title">
          <h2>Profil</h2>
          <p>Visi Sekolah</p>
        </div>
        <p class="mb-5 text-center" style="color:#444; line-height:1.8; font-size:1.15rem; font-weight:500; font-style:italic;">
          " <?= $visi !== '' ? nl2br(htmlspecialchars($visi)) : '[Visi belum diisi di pengaturan admin]' ?> "
        </p>

        <div class="section-title">
          <h2>Profil</h2>
          <p>Misi Sekolah</p>
        </div>
        <?php if (empty($misi_lines)): ?>
          <p class="text-muted"><em>[Misi belum diisi di pengaturan admin]</em></p>
        <?php else: ?>
          <div class="row g-3 mb-5">
            <?php foreach ($misi_lines as $line): ?>
              <div class="col-md-6">
                <div class="card premium-list-card p-3 shadow-sm h-100 rounded-3 border-0 d-flex flex-row align-items-start">
                  <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center me-3" style="min-width: 32px; height: 32px;">
                    <i class="bi bi-patch-check-fill" style="font-size: 16px; color: #1acc8d;"></i>
                  </div>
                  <div class="text-dark leading-relaxed" style="font-size: 14.5px;"><?= htmlspecialchars($line) ?></div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <div class="section-title pt-4">
          <h2>Profil</h2>
          <p>12 Quality Assurance</p>
        </div>
        <?php if (empty($qa_lines)): ?>
          <p class="text-muted"><em>[QA list belum diisi di pengaturan admin]</em></p>
        <?php else: ?>
          <div class="row g-3">
            <?php foreach ($qa_lines as $index => $line): ?>
              <div class="col-lg-4 col-md-6">
                <div class="card premium-list-card p-3.5 shadow-sm h-100 rounded-3 border-0 d-flex flex-row align-items-center">
                  <span class="qa-number me-2"><?= sprintf("%02d", $index + 1) ?></span>
                  <div class="text-dark fw-semibold" style="font-size: 14px;"><?= htmlspecialchars($line) ?></div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

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
