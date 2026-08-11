<?php
require_once __DIR__ . '/includes/public_init.php';

function get_indonesian_month_name($month_num) {
    $months = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    return $months[(int)$month_num] ?? '';
}

function format_month_year($year_month) {
    $parts = explode('-', $year_month);
    if (count($parts) === 2) {
        $year = $parts[0];
        $month = get_indonesian_month_name($parts[1]);
        return "$month $year";
    }
    return $year_month;
}

$selected_month = trim($_GET['bulan'] ?? '');
$is_filtered = false;
$filter_month_formatted = '';

if ($selected_month !== '' && preg_match('/^\d{4}-\d{2}$/', $selected_month)) {
    $is_filtered = true;
    $filter_month_formatted = format_month_year($selected_month);
}

$current_month = date('Y-m');
$per_page = 6;
$page = max(1, (int) ($_GET['page'] ?? 1));

$total = 0;
$news_items = [];
$archive_months = [];

try {
    if ($is_filtered) {
        // Paginasi untuk arsip bulan tertentu
        $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM news WHERE status = 'published' AND DATE_FORMAT(created_at, '%Y-%m') = ?");
        $stmtCount->execute([$selected_month]);
        $total = (int) $stmtCount->fetchColumn();
    } else {
        // Paginasi untuk bulan berjalan
        $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM news WHERE status = 'published' AND DATE_FORMAT(created_at, '%Y-%m') = ?");
        $stmtCount->execute([$current_month]);
        $total = (int) $stmtCount->fetchColumn();
    }
} catch (PDOException $e) {
    $total = 0;
}

$total_pages = max(1, (int) ceil($total / $per_page));
if ($page > $total_pages) {
    $page = $total_pages;
}
$offset = ($page - 1) * $per_page;

try {
    if ($is_filtered) {
        $stmt = $pdo->prepare(
            "SELECT title, slug, image, created_at FROM news WHERE status = 'published' AND DATE_FORMAT(created_at, '%Y-%m') = :bulan ORDER BY created_at DESC LIMIT :limit OFFSET :offset"
        );
        $stmt->bindValue(':bulan', $selected_month);
        $stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $news_items = $stmt->fetchAll();
    } else {
        $stmt = $pdo->prepare(
            "SELECT title, slug, image, created_at FROM news WHERE status = 'published' AND DATE_FORMAT(created_at, '%Y-%m') = :current_month ORDER BY created_at DESC LIMIT :limit OFFSET :offset"
        );
        $stmt->bindValue(':current_month', $current_month);
        $stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $news_items = $stmt->fetchAll();
    }
} catch (PDOException $e) {
    $news_items = [];
}

// Ambil pengelompokan arsip bulan-bulan sebelumnya
try {
    $stmtArchive = $pdo->prepare(
        "SELECT DATE_FORMAT(created_at, '%Y-%m') as month_val, COUNT(*) as news_count FROM news WHERE status = 'published' AND DATE_FORMAT(created_at, '%Y-%m') < :current_month GROUP BY month_val ORDER BY month_val DESC"
    );
    $stmtArchive->execute([':current_month' => $current_month]);
    $archive_months = $stmtArchive->fetchAll();
} catch (PDOException $e) {
    $archive_months = [];
}

$page_title = 'Berita';
$is_home = false;
$is_berita = true;
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Berita - SDIT AL FATAH</title>
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
          <h2>Berita</h2>
          <ol>
            <li><a href="index.php">Beranda</a></li>
            <li>Berita</li>
          </ol>
        </div>
      </div>
    </section>

    <section id="news" class="news inner-page">
      <div class="container">

        <div class="section-title" data-aos="fade-up">
          <h2>Berita</h2>
          <p>
            <?php if ($is_filtered): ?>
                Arsip Berita: <?= htmlspecialchars($filter_month_formatted) ?>
            <?php else: ?>
                Berita Bulan Ini (<?= format_month_year($current_month) ?>)
            <?php endif; ?>
          </p>
        </div>

        <?php if ($is_filtered): ?>
            <div class="mb-4" data-aos="fade-up">
                <a href="berita.php" class="btn btn-brand btn-sm text-white px-3"><i class="fa-solid fa-newspaper me-1"></i> Tampilkan Berita Terbaru (Bulan Ini)</a>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <!-- Left Column: News Grid -->
            <div class="col-lg-8" data-aos="fade-up" data-aos-delay="100">
                <div class="row gy-4">
                  <?php if (empty($news_items)): ?>
                  <div class="col-12 text-center py-4">
                    <p class="text-muted mb-0">Belum ada berita dipublikasikan untuk periode ini.</p>
                  </div>
                  <?php else: ?>
                  <?php
                  $news_delay = 100;
                  foreach ($news_items as $item):
                    $has_image = !empty($item['image']);
                    $img_src = $has_image ? ('admin/uploads/' . $item['image']) : 'assets/img/logo afix.png';
                    $href = 'inner-page.php?slug=' . urlencode($item['slug']);
                  ?>
                  <div class="col-md-6" data-aos="zoom-in" data-aos-delay="<?= (int) $news_delay ?>">
                    <a href="<?= htmlspecialchars($href) ?>" class="news-item">
                      <div class="news-img">
                        <img src="<?= htmlspecialchars($img_src) ?>" alt="<?= htmlspecialchars($item['title']) ?>">
                      </div>
                      <div class="news-info">
                        <h4><?= htmlspecialchars($item['title']) ?></h4>
                        <span class="news-date"><?= date('d F Y', strtotime($item['created_at'])) ?></span>
                      </div>
                    </a>
                  </div>
                  <?php
                    $news_delay += 50;
                  endforeach;
                  ?>
                  <?php endif; ?>
                </div>

                <?php if ($total_pages > 1): ?>
                <nav class="mt-5 d-flex justify-content-center" aria-label="Paginasi berita">
                  <ul class="pagination">
                    <li class="page-item<?= $page <= 1 ? ' disabled' : '' ?>">
                      <a class="page-link" href="?<?= $is_filtered ? 'bulan=' . urlencode($selected_month) . '&' : '' ?>page=<?= $page - 1 ?>">Sebelumnya</a>
                    </li>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item<?= $i === $page ? ' active' : '' ?>">
                      <a class="page-link" href="?<?= $is_filtered ? 'bulan=' . urlencode($selected_month) . '&' : '' ?>page=<?= $i ?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>
                    <li class="page-item<?= $page >= $total_pages ? ' disabled' : '' ?>">
                      <a class="page-link" href="?<?= $is_filtered ? 'bulan=' . urlencode($selected_month) . '&' : '' ?>page=<?= $page + 1 ?>">Selanjutnya</a>
                    </li>
                  </ul>
                </nav>
                <?php endif; ?>
            </div>

            <!-- Right Column: Sidebar Archives -->
            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card border-0 rounded-4 shadow-sm p-4 bg-light">
                    <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-box-archive text-success me-2" style="color: #1acc8d !important;"></i> Arsip Berita</h5>
                    <p class="text-muted small mb-3">Daftar berita dari bulan-bulan sebelumnya:</p>
                    <hr class="mt-0 mb-3 border-secondary border-opacity-25">
                    
                    <?php if (empty($archive_months)): ?>
                        <p class="text-muted small mb-0">Tidak ada arsip berita lama.</p>
                    <?php else: ?>
                        <div class="list-group list-group-flush rounded-3 overflow-hidden">
                            <?php foreach ($archive_months as $arch): ?>
                                <a href="berita.php?bulan=<?= htmlspecialchars($arch['month_val']) ?>" class="list-group-item list-group-item-action border-0 px-2 d-flex justify-content-between align-items-center bg-transparent <?= ($selected_month === $arch['month_val']) ? 'text-success fw-bold' : 'text-dark' ?>">
                                    <span><i class="fa-regular fa-calendar-check text-secondary me-2"></i><?= format_month_year($arch['month_val']) ?></span>
                                    <span class="badge rounded-pill bg-success bg-opacity-10 text-success"><?= (int) $arch['news_count'] ?> berita</span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
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
