<?php
require_once __DIR__ . '/includes/public_init.php';
$page_title = 'Struktur Organisasi';
$is_home = false;

$struktur = [];
try {
    $stmt = $pdo->query("SELECT position_title, person_name FROM org_structure ORDER BY display_order ASC, id ASC");
    $rows = $stmt->fetchAll();
    foreach ($rows as $row) {
        $name = trim($row['person_name'] ?? '');
        if ($name === '') {
            $name = '[' . $row['position_title'] . ']';
        }
        $struktur[] = [
            'role' => $row['position_title'],
            'name' => $name
        ];
    }
} catch (PDOException $e) {
    $struktur = [];
}
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
