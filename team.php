<?php
require_once __DIR__ . '/includes/public_init.php';
$page_title = 'Team';
$is_home = false;

$team_members = [];
try {
    $stmt = $pdo->query("SELECT name, position, photo FROM team ORDER BY display_order ASC, id ASC");
    $team_members = $stmt->fetchAll();
} catch (PDOException $e) {
    $team_members = [];
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Team &amp; Guru - SDIT AL FATAH</title>
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
          <h2>Team &amp; Guru</h2>
          <ol>
            <li><a href="index.php">Beranda</a></li>
            <li>Team</li>
          </ol>
        </div>
      </div>
    </section>

    <!-- ======= Team Section ======= -->
    <section id="team" class="team inner-page">
      <div class="container" data-aos="fade-up">
        <div class="section-title">
          <h2>Profil</h2>
          <p>Manajemen &amp; Guru Sekolah</p>
        </div>

        <?php if (empty($team_members)): ?>
        <div class="row" data-aos="fade-left">
          <div class="col-12 text-center py-5 text-muted">
            <i class="fa-solid fa-users fa-3x mb-3 opacity-25"></i>
            <p class="mb-0">Belum ada data tim. Kelola anggota tim melalui admin panel.</p>
          </div>
        </div>
        <?php else: ?>
        <?php
        $team_rows = array_chunk($team_members, 4);
        $row_index = 0;
        foreach ($team_rows as $row_members):
          $row_index++;
        ?>
        <?php if ($row_index > 1): ?>
        <div class="mt-5"></div>
        <?php endif; ?>
        <div class="row" data-aos="fade-left">
          <?php
          $col_classes = ['', 'mt-5 mt-md-0', 'mt-5 mt-lg-0', 'mt-5 mt-lg-0'];
          $delay_base = 100;
          foreach ($row_members as $col_index => $member):
            $delay = $delay_base + ($col_index * 100);
            $col_class = $col_classes[$col_index] ?? 'mt-5 mt-lg-0';
            if (!empty($member['photo']) && file_exists('admin/uploads/' . $member['photo'])) {
              $photo_src = 'admin/uploads/' . $member['photo'];
            } else {
              $fallback = ($col_index % 4) + 1;
              $photo_src = 'assets/img/team/team-' . $fallback . '.jpg';
            }
          ?>
          <div class="col-lg-3 col-md-6<?= $col_class !== '' ? ' ' . $col_class : '' ?>">
            <div class="member" data-aos="zoom-in" data-aos-delay="<?= (int) $delay ?>">
              <div class="pic"><img src="<?= htmlspecialchars($photo_src) ?>" class="img-fluid" alt="<?= htmlspecialchars($member['name']) ?>" style="object-position: <?= htmlspecialchars($member['photo_position'] ?? 'center') ?>;"></div>
              <div class="member-info">
                <h4><?= htmlspecialchars($member['name']) ?></h4>
                <span><?= htmlspecialchars($member['position']) ?></span>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </section><!-- End Team Section -->
  </main>

  <?php include __DIR__ . '/includes/public_footer.php'; ?>

  <?php include __DIR__ . '/includes/public_wa_float.php'; ?>
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/js/main.js"></script>
</body>
</html>
