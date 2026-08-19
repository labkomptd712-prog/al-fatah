<?php
require_once __DIR__ . '/includes/public_init.php';

$gallery_items = [];
try {
    // Ambil ID kategori umum
    $stmtUmum = $pdo->query("SELECT id FROM gallery_categories WHERE slug = 'umum'");
    $umum_id = $stmtUmum->fetchColumn();
    
    if ($umum_id) {
        $stmt = $pdo->prepare("SELECT id, caption, image FROM gallery WHERE category_id = ? ORDER BY created_at DESC");
        $stmt->execute([$umum_id]);
        $gallery_items = $stmt->fetchAll();
    } else {
        // Fallback jika kategori umum tidak ditemukan
        $stmt = $pdo->query("SELECT id, caption, image FROM gallery ORDER BY created_at DESC");
        $gallery_items = $stmt->fetchAll();
    }
} catch (PDOException $e) {
    $gallery_items = [];
}

$team_members = [];
try {
    $stmt = $pdo->query("SELECT name, position, photo FROM team ORDER BY display_order ASC, id ASC");
    $team_members = $stmt->fetchAll();
} catch (PDOException $e) {
    $team_members = [];
}

$is_home = true;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>SDIT AL FATAH</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/logo afix.png" rel="icon">
  <link href="assets/img/logo afix.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Montserrat:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Font Awesome 6.5.1 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/style.css?v=1.3" rel="stylesheet">

  <!-- =======================================================
  * Template Name: Bootslander
  * Template URL: https://bootstrapmade.com/bootslander-free-bootstrap-landing-page-template/
  * Updated: Mar 17 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="fixed-top d-flex align-items-center header-transparent">
    <div class="container d-flex align-items-center justify-content-between">

      <div class="logo">
        <a href="index.php"><img src="assets/img/logo afix.png" alt="" class="img-fluid" ></a>
        <h1><a href="index.php"><span><I>SDIT AL FATAH</I></span></a></h1> 
        <!-- Uncomment below if you prefer to use an image logo -->
      </div>

      <!-- navbar-->
      <?php include __DIR__ . '/includes/public_nav.php'; ?>

    </div>
  </header><!-- End Header -->

  <!-- ======= Hero Section ======= -->
  <section id="hero">

    <div class="container">
      <div class="row justify-content-between">
        <div class="col-lg-7 pt-5 pt-lg-0 order-2 order-lg-1 d-flex align-items-center">
          <div data-aos="zoom-out">
          <h1>Selamat Datang Di Website <span>SDIT AL FATAH </span></h1>
            <h2>Menghadirkan kemudahan akses informasi yang cepat dan tepat, untuk dunia pendidikan yang lebih baik</h2>
            <div class="text-center text-lg-start">
              <a href="profil-sekolah.php" class="btn-get-started">Profil Sekolah</a>
            </div>
          </div>
        </div>
        <div class="col-lg-4 order-1 order-lg-2 hero-img" data-aos="zoom-out" data-aos-delay="300">
          <img src="assets/img/alfatah header.jpeg" class="img-fluid animated" alt="">
        </div>
      </div>
    </div>

    <svg class="hero-waves" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 24 150 28 " preserveAspectRatio="none">
      <defs>
        <path id="wave-path" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z">
      </defs>
      <g class="wave1">
        <use xlink:href="#wave-path" x="50" y="3" fill="rgba(255,255,255, .1)">
      </g>
      <g class="wave2">
        <use xlink:href="#wave-path" x="50" y="0" fill="rgba(255,255,255, .2)">
      </g>
      <g class="wave3">
        <use xlink:href="#wave-path" x="50" y="9" fill="#fff">
      </g>
    </svg>

  </section><!-- End Hero -->

  <main id="main">

    <!-- ======= About Section ======= -->
    <section id="about" class="about">
      <div class="container-fluid">

        <div class="row">
          <div class="col-xl-5 col-lg-6 video-box d-flex justify-content-center align-items-stretch" data-aos="fade-right">
            <a href="https://youtu.be/UX-yxiR9N6k?si=yP8n0XGDRsX_uOFO" class="glightbox play-btn mb-4"></a>
          </div>
          <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.1411001643646!2d107.0311234745709!3d-6.245129293743245!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e698ef399705e7d%3A0x681a00ccf5ed2046!2sSEKOLAH%20ISLAM%20TERPADU%20AL%20FATAH!5e0!3m2!1sid!2sid!4v1720539762308!5m2!1sid!2sid" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
          <div class="col-xl-7 col-lg-6 icon-boxes d-flex flex-column align-items-stretch justify-content-center py-5 px-lg-5" data-aos="fade-left">
            <h3>Alamat SDIT Al Fatah</h3>
            <p>Jl. Masjid Al-Muawanah No.60, RT.006/RW.012, Aren Jaya, Kec. Bekasi Tim., Kota Bks, Jawa Barat 17111</p>

            <div class="icon-box" data-aos="zoom-in" data-aos-delay="100">
              <div class="icon"><i class='bx bx-grid-alt'></i></div>
              <h4 class="title"><a href="">Sarana Prasarana</a></h4>
              <p class="description">"Manfaatkan sarana dan prasarana lengkap kami untuk mendukung pembelajaran dan aktivitasmu di sekolah!"</p>
            </div>

            <div class="icon-box" data-aos="zoom-in" data-aos-delay="200">
              <div class="icon"><i class='bx bx-sort-up'></i></div>
              <h4 class="title"><a href="">banyak Pilihan Ekskul</a></h4>
              <p class="description">
                "Temukan passion dan bakatmu melalui beragam pilihan ekskul yang seru dan bermanfaat di sekolah!"</p>
            </div>

            <div class="icon-box" data-aos="zoom-in" data-aos-delay="300">
              <div class="icon"><i class='bx bx-been-here'></i></div>
              <h4 class="title"><a href="">Kegiatan Diluar kelas</a></h4>
              <p class="description">"Jelajahi minatmu dan kembangkan kemampuan melalui berbagai kegiatan seru di luar sekolah!"</p>
            </div>

          </div>
        </div>

      </div>
    </section><!-- End About Section -->

    <!-- ======= Features Section ======= -->
    <section id="features" class="features">
      <div class="container">

        <div class="section-title" data-aos="fade-up">
          <h2>Kelengkapan</h2>
          <p>Fasilitas</p>
        </div>

        <?php
        try {
            $stmtFac = $pdo->query("SELECT * FROM facilities ORDER BY display_order ASC, name ASC");
            $facilities_list = $stmtFac->fetchAll();
        } catch (PDOException $e) {
            $facilities_list = [];
        }

        $icon_map = [
            'lapangan' => ['icon' => 'bx bx-map-pin', 'color' => '#ff5828'],
            'lab ptd' => ['icon' => 'ri-bar-chart-box-line', 'color' => '#5578ff'],
            'komputer' => ['icon' => 'bx bx-desktop', 'color' => 'rgb(18, 123, 152)'],
            'masjid' => ['icon' => 'bx bx-home', 'color' => 'rgb(47, 134, 6)'],
            'wc' => ['icon' => 'bx bx-male-female', 'color' => 'rgb(183, 84, 245)'],
            'flying' => ['icon' => 'bx bxs-cable-car', 'color' => 'rgb(0, 87, 200)'],
            'ac' => ['icon' => 'bx bx-wind', 'color' => '#24c3d5'],
            'kantin' => ['icon' => 'bx bxs-store', 'color' => '#ff5828'],
            'belajar' => ['icon' => 'bx bxs-layer', 'color' => '#85b20a'],
            'perpustakaan' => ['icon' => 'bx bxs-book-bookmark', 'color' => '#089b79'],
            'hotspot' => ['icon' => 'ri-base-station-line', 'color' => '#ff5828'],
            'taman' => ['icon' => 'bx bxs-tree', 'color' => '#29cc61'],
        ];
        ?>
        <div class="row" data-aos="fade-left">
          <?php if (empty($facilities_list)): ?>
            <div class="col-12 text-center py-4 text-muted">Belum ada data fasilitas.</div>
          <?php else: ?>
            <?php 
            $delay = 50; 
            foreach ($facilities_list as $index => $fac): 
                $lower_name = strtolower($fac['name']);
                $icon_class = 'bx bx-check-circle';
                $icon_color = '#1acc8d';
                
                foreach ($icon_map as $key => $val) {
                    if (strpos($lower_name, $key) !== false) {
                        $icon_class = $val['icon'];
                        $icon_color = $val['color'];
                        break;
                    }
                }
                
                $mt_class = ' mt-4';
                if ($index < 3) {
                    $mt_class .= ' mt-md-0';
                }
                if ($index < 4) {
                    $mt_class .= ' mt-lg-0';
                }
            ?>
              <div class="col-lg-3 col-md-4<?= $mt_class ?>">
                <div class="icon-box" data-aos="zoom-in" data-aos-delay="<?= $delay ?>">
                  <i class='<?= $icon_class ?>' style="color: <?= $icon_color ?>;" ></i>
                  <h3><a href="fasilitas.php"><?= htmlspecialchars($fac['name']) ?></a></h3>
                </div>
              </div>
            <?php 
              $delay += 50;
            endforeach; 
            ?>
          <?php endif; ?>
        </div>

      </div>
    </section><!-- End Features Section -->

    <!-- ======= Counts Section ======= -->
    <section id="counts" class="counts">
      <div class="container">

        <div class="section-title" data-aos="fade-up">
          <h2>Statistik Sekolah</h2>
          <p>Informasi Data SDIT Al Fatah</p>
        </div>

        <div class="row" data-aos="fade-up">

          <div class="col-lg-3 col-md-6">
            <div class="count-box">
              <i class='bx bx-user-circle'></i>
              <span data-purecounter-start="0" data-purecounter-end="<?= htmlspecialchars($settings['stats_siswa']) ?>" data-purecounter-duration="1" class="purecounter"></span>
              <p>Peserta Didik</p>
            </div>
          </div>

          <div class="col-lg-3 col-md-6 mt-5 mt-md-0">
            <div class="count-box">
              <i class="bi bi-journal-richtext"></i>
              <span data-purecounter-start="0" data-purecounter-end="<?= htmlspecialchars($settings['stats_guru']) ?>" data-purecounter-duration="1" class="purecounter"></span>
              <p>Pendidik</p>
            </div>
          </div>

          <div class="col-lg-3 col-md-6 mt-5 mt-lg-0">
            <div class="count-box">
              <i class="bi bi-headset"></i>
              <span data-purecounter-start="0" data-purecounter-end="<?= htmlspecialchars($settings['stats_tendik']) ?>" data-purecounter-duration="1" class="purecounter"></span>
              <p>Tendik</p>
            </div>
          </div>

          <div class="col-lg-3 col-md-6 mt-5 mt-lg-0">
            <div class="count-box">
              <i class="bi bi-people"></i>
              <span data-purecounter-start="0" data-purecounter-end="<?= htmlspecialchars($settings['stats_sarpras']) ?>" data-purecounter-duration="1" class="purecounter"></span>
              <p>Ruang Sarpras</p>
            </div>
          </div>

        </div>

      </div>
    </section><!-- End Counts Section -->

    <!-- ======= Details Section ======= -->
    <section id="details" class="details">
      <div class="container">

        <div class="row content">
          <div class="col-md-4" data-aos="fade-right">
            <img src="assets/img/alfatah oudoor.jpeg" class="img-fluid" alt="">
          </div>
          <div class="col-md-8 pt-4" data-aos="fade-up">
            <h3>Fasilitas Unggulan untuk Mendukung Kesuksesan Belajar dan Berkarya</h3>
            <p class="fst-italic">
              Fasilitas unggulan kami mencakup ruang kelas modern, laboratorium canggih, perpustakaan lengkap, lapangan olahraga, dan area seni yang dirancang untuk menciptakan lingkungan belajar yang optimal dan inspiratif bagi setiap siswa.
            </p>
            <ul>
              <li><i class="bi bi-check"></i> Dilengkapi dengan teknologi canggih untuk mendukung proses belajar mengajar.</li>
              <li><i class="bi bi-check"></i> Area luas untuk berbagai kegiatan olahraga seperti sepak bola, basket, dan atletik.</li>
              <li><i class="bi bi-check"></i> Area hijau untuk relaksasi dan kegiatan outdoor.</li>
              <li><i class="bi bi-check"></i> Koleksi buku dan sumber daya digital yang luas untuk mendukung penelitian dan pembelajaran.</li>
            </ul>
            <p>
              "Fasilitas unggulan ini dirancang untuk menciptakan lingkungan belajar yang inspiratif, mendukung perkembangan akademik dan non-akademik siswa, serta memaksimalkan potensi mereka untuk meraih prestasi gemilang."
            </p>
          </div>
        </div>

        <div class="row content">
          <div class="col-md-4 order-1 order-md-2" data-aos="fade-left">
            <img src="assets/img/alfatah baju olahraga.jpeg" class="img-fluid" alt="">
          </div>
          <div class="col-md-8 pt-5 order-2 order-md-1" data-aos="fade-up">
            <h3>Kegiatan Outdoor</h3>
            <p class="fst-italic">
              Tantang diri kamu dengan berbagai aktivitas outbound yang seru dan menantang
            </p>
            <p>
             
SDIT Al Fatah dengan bangga mempersembahkan berbagai kegiatan outdoor yang dirancang untuk memperkaya pengalaman belajar siswa. Di taman bermain edukatif, anak-anak dapat mengembangkan keterampilan motorik dan sosial melalui permainan seperti perosotan, ayunan, dan papan panjat mini. Area pertanian mini memberikan kesempatan bagi siswa untuk belajar tentang bercocok tanam dan ekosistem, dengan kebun sayur dan buah serta kolam ikan. Aktivitas ini tidak hanya mengajarkan tanggung jawab tetapi juga menumbuhkan kecintaan terhadap alam.
            </p>
            <p>
              Selain itu, lapangan olahraga lengkap seperti lapangan sepak bola, bola basket, dan lintasan lari mendukung kesehatan siswa. Kegiatan outbound seperti flying fox dan jembatan tali melatih keberanian dan koordinasi. Dengan taman sains dan ruang kelas terbuka, siswa dapat melakukan eksperimen ilmiah dalam lingkungan yang inspiratif. Kegiatan ini menciptakan pengalaman belajar yang holistik dan menyenangkan.
            </p>
          </div>
        </div>

        <div class="row content">
          <div class="col-md-4" data-aos="fade-right">
            <img src="assets/img/alfatah ngaji.jpeg" class="img-fluid" alt="">
          </div>
          <div class="col-md-8 pt-5" data-aos="fade-up">
            <h3>Mengaji Bersama : Langkah Pertama Menuju Keimanan</h3>
            <p>Menggali Makna Al-Qur'an dengan Cinta dan Kebersamaan</p>
            <ul>
              <li><i class="bi bi-check"></i> Pendekatan Interaktif dan Menyenangkan</li>
              <li><i class="bi bi-check"></i>  Pembelajaran Terintegrasi dengan Nilai-Nilai Islami</li>
              <li><i class="bi bi-check"></i> Bimbingan oleh Pengajar Berpengalaman</li>
            </ul>
            <p>
              Program mengaji di SDIT Al Fatah memiliki keunggulan yang signifikan dalam pendekatan pembelajaran yang interaktif dan menyenangkan. Melalui penggunaan permainan edukatif, cerita-cerita menarik, dan aktivitas kreatif, anak-anak tidak hanya belajar membaca Al-Qur'an tetapi juga merasakan kegembiraan dalam proses belajar mereka. Pendekatan ini membantu meningkatkan motivasi belajar mereka sehingga mereka dapat dengan cepat memahami dan menghafal ayat-ayat Al-Qur'an dengan lebih baik.
            </p>
            <p>
              Selain itu, program mengaji di SDIT Al Fatah juga dikenal karena integrasi yang kuat dengan nilai-nilai Islami. Selain pembelajaran membaca Al-Qur'an, anak-anak juga diajarkan tentang akhlak mulia, kasih sayang, dan tanggung jawab, yang diterapkan dalam kehidupan sehari-hari mereka. Hal ini membantu membentuk karakter yang kuat dan berbudi pekerti luhur pada anak-anak, mempersiapkan mereka menjadi individu yang bertanggung jawab dan berkontribusi positif dalam masyarakat.
            </p>
          </div>
        </div>

        <div class="row content">
          <div class="col-md-4 order-1 order-md-2" data-aos="fade-left">
            <img src="assets/img/alfatah siaga.jpeg" class="img-fluid" alt="">
          </div>
          <div class="col-md-8 pt-5 order-2 order-md-1" data-aos="fade-up">
            <h3>Eksplorasi Alam dan Kebaikan : Petualangan Pramuka di SDIT Al Fatah</h3>
            <p class="fst-italic">
              Bersama Pramuka, Menciptakan Generasi Unggul dan Berkarakter
            </p>
            <p>
              Menapaki Jejak Kegigihan dan Kreativitas Anak-anak": Melalui kegiatan Pramuka di SDIT Al Fatah, anak-anak diajak untuk mengeksplorasi jejak kegigihan dan kreativitas mereka. Dalam petualangan di alam terbuka, mereka belajar untuk menghadapi tantangan dengan keberanian dan keteguhan hati, sambil juga mengembangkan kreativitas dalam menyelesaikan berbagai misi dan kegiatan Pramuka yang menarik. Subjudul ini menggambarkan fokus pada pembangunan karakter yang tangguh dan kreatif, sesuai dengan nilai-nilai Pramuka yang menekankan pengembangan diri secara menyeluruh
            </p>
            <ul>
              <li><i class="bi bi-check"></i> Pembentukan Karakter Unggul .</li>
              <li><i class="bi bi-check"></i> Eksplorasi Alam dan Lingkungan.</li>
              <li><i class="bi bi-check"></i> Pengembangan Keterampilan Kehidupan</li>
            </ul>
          </div>
        </div>

      </div>
    </section><!-- End Details Section -->

    <!-- ======= Gallery Section ======= -->
    <section id="gallery" class="gallery">
      <div class="container">

        <div class="section-title" data-aos="fade-up">
          <h2>Gallery</h2>
          <p>Check our Gallery</p>
        </div>

        <div class="row g-0" data-aos="fade-left">
          <?php if (empty($gallery_items)): ?>
          <div class="col-12 text-center py-4">
            <p class="text-muted mb-0">Belum ada foto di kategori Umum.</p>
          </div>
          <?php else: ?>
          <?php
          $delay = 100;
          foreach ($gallery_items as $photo):
            $img_path = 'admin/uploads/' . $photo['image'];
            $alt = ($photo['caption'] !== '' && $photo['caption'] !== null)
              ? $photo['caption']
              : 'Galeri SDIT Al Fatah';
          ?>
          <div class="col-lg-3 col-md-4">
            <div class="gallery-item" data-aos="zoom-in" data-aos-delay="<?= (int) $delay ?>">
              <a href="<?= htmlspecialchars($img_path) ?>" class="gallery-lightbox" title="<?= htmlspecialchars($alt) ?>">
                <img src="<?= htmlspecialchars($img_path) ?>" alt="<?= htmlspecialchars($alt) ?>" class="img-fluid">
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
    </section><!-- End Gallery Section -->

    <!-- ======= Testimonials Title Section ======= -->
    <section id="testimonials-title" class="testimonials-title" style="padding: 60px 0 0 0; background: #fff;">
      <div class="container">
        <div class="section-title" data-aos="fade-up" style="padding-bottom: 0;">
          <h2>Testimoni Alumni</h2>
          <p>Kesan & Pesan Alumni Sekolah</p>
        </div>
      </div>
    </section>

    <!-- ======= Testimonials Section ======= -->
    <section id="testimonials" class="testimonials" style="padding: 40px 0 60px 0;">
      <div class="container">

        <div class="testimonials-slider swiper" data-aos="fade-up" data-aos-delay="100">
          <div class="swiper-wrapper">
            <?php
            try {
                $stmtTestimonials = $pdo->query("SELECT * FROM testimonials ORDER BY display_order ASC, id ASC");
                $db_testimonials = $stmtTestimonials->fetchAll();
            } catch (PDOException $e) {
                $db_testimonials = [];
            }
            
            if (!empty($db_testimonials)):
                foreach ($db_testimonials as $t):
                    $photo_url = !empty($t['photo']) ? 'admin/uploads/' . $t['photo'] : 'assets/img/testimonials/testimonials-1.jpg';
            ?>
                <div class="swiper-slide">
                  <div class="testimonial-item">
                    <div class="testimonial-avatar">
                      <img src="<?= htmlspecialchars($photo_url) ?>" alt="" style="object-position: <?= htmlspecialchars($t['photo_position'] ?? 'center') ?>;">
                    </div>
                    <h3><?= htmlspecialchars($t['name']) ?></h3>
                    <h4><?= htmlspecialchars($t['position']) ?></h4>
                    <p>
                      <i class="bx bxs-quote-alt-left quote-icon-left"></i>
                      <?= htmlspecialchars($t['quote']) ?>
                      <i class="bx bxs-quote-alt-right quote-icon-right"></i>
                    </p>
                  </div>
                </div>
            <?php 
                endforeach;
            else:
            ?>
                <div class="swiper-slide">
                  <div class="testimonial-item">
                    <div class="testimonial-avatar">
                      <img src="assets/img/testimonials/testimonials-1.jpg" alt="">
                    </div>
                    <h3>irfan hp</h3>
                    <h4>admin&amp; user</h4>
                    <p>
                      <i class="bx bxs-quote-alt-left quote-icon-left"></i>
                      Setiap anak adalah permata yang berharga, bersinar terang dengan bimbingan yang tepat.
                      <i class="bx bxs-quote-alt-right quote-icon-right"></i>
                    </p>
                  </div>
                </div><!-- End testimonial item -->

                <div class="swiper-slide">
                  <div class="testimonial-item">
                    <div class="testimonial-avatar">
                      <img src="assets/img/testimonials/testimonials-2.jpg" alt="">
                    </div>
                    <h3>irfan cv</h3>
                    <h4>Designer</h4>
                    <p>
                      <i class="bx bxs-quote-alt-left quote-icon-left"></i>
                      Dengan ilmu kita terangi dunia, dengan iman kita kuatkan jiwa
                      <i class="bx bxs-quote-alt-right quote-icon-right"></i>
                    </p>
                  </div>
                </div><!-- End testimonial item -->

                <div class="swiper-slide">
                  <div class="testimonial-item">
                    <div class="testimonial-avatar">
                      <img src="assets/img/testimonials/testimonials-3.jpg" alt="">
                    </div>
                    <h3>irfan jpg</h3>
                    <h4>guru</h4>
                    <p>
                      <i class="bx bxs-quote-alt-left quote-icon-left"></i>
                      Menanam benih pengetahuan, memanen generasi emas
                      <i class="bx bxs-quote-alt-right quote-icon-right"></i>
                    </p>
                  </div>
                </div><!-- End testimonial item -->

                <div class="swiper-slide">
                  <div class="testimonial-item">
                    <div class="testimonial-avatar">
                      <img src="assets/img/testimonials/testimonials-4.jpg" alt="">
                    </div>
                    <h3>irfan png</h3>
                    <h4>pebasket handal</h4>
                    <p>
                      <i class="bx bxs-quote-alt-left quote-icon-left"></i>
                      Menginspirasi setiap langkah kecil menuju mimpi besar
                      <i class="bx bxs-quote-alt-right quote-icon-right"></i>
                    </p>
                  </div>
                </div><!-- End testimonial item -->

                <div class="swiper-slide">
                  <div class="testimonial-item">
                    <div class="testimonial-avatar">
                      <img src="assets/img/testimonials/testimonials-5.jpg" alt="">
                    </div>
                    <h3>irfan mandor</h3>
                    <h4>mandor</h4>
                    <p>
                      <i class="bx bxs-quote-alt-left quote-icon-left"></i>
                      Membimbing dengan hati, mengajar dengan cinta.
                      <i class="bx bxs-quote-alt-right quote-icon-right"></i>
                    </p>
                  </div>
                </div><!-- End testimonial item -->
            <?php endif; ?>
          </div>
          <div class="swiper-pagination"></div>
        </div>
      </div>
    </section><!-- End Testimonials Section -->

    <!-- ======= Team Section ======= -->
    <section id="team" class="team">
      <div class="container">
        <div class="section-title" data-aos="fade-up">
          <h2>Team</h2>
          <p>Manajemen Sekolah</p>
        </div>

        <?php if (empty($team_members)): ?>
        <div class="row" data-aos="fade-left">
          <div class="col-12 text-center py-4">
            <p class="text-muted mb-0">Belum ada data tim. Kelola anggota tim melalui admin panel.</p>
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
      </div>
    </section>

    <section id="team" class="team">
      <div class="container">
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

    
    <!-- ======= Pricing Section =======
    <section id="pricing" class="pricing">
      <div class="container">

        <div class="section-title" data-aos="fade-up">
          <h2>Pricing</h2>
          <p>Check our Pricing</p>
        </div>

        <div class="row" data-aos="fade-left">

          <div class="col-lg-3 col-md-6">
            <div class="box" data-aos="zoom-in" data-aos-delay="100">
              <h3>Free</h3>
              <h4><sup>$</sup>0<span> / month</span></h4>
              <ul>
                <li>Aida dere</li>
                <li>Nec feugiat nisl</li>
                <li>Nulla at volutpat dola</li>
                <li class="na">Pharetra massa</li>
                <li class="na">Massa ultricies mi</li>
              </ul>
              <div class="btn-wrap">
                <a href="#" class="btn-buy">Buy Now</a>
              </div>
            </div>
          </div>

          <div class="col-lg-3 col-md-6 mt-4 mt-md-0">
            <div class="box featured" data-aos="zoom-in" data-aos-delay="200">
              <h3>Business</h3>
              <h4><sup>$</sup>19<span> / month</span></h4>
              <ul>
                <li>Aida dere</li>
                <li>Nec feugiat nisl</li>
                <li>Nulla at volutpat dola</li>
                <li>Pharetra massa</li>
                <li class="na">Massa ultricies mi</li>
              </ul>
              <div class="btn-wrap">
                <a href="#" class="btn-buy">Buy Now</a>
              </div>
            </div>
          </div>

          <div class="col-lg-3 col-md-6 mt-4 mt-lg-0">
            <div class="box" data-aos="zoom-in" data-aos-delay="300">
              <h3>Developer</h3>
              <h4><sup>$</sup>29<span> / month</span></h4>
              <ul>
                <li>Aida dere</li>
                <li>Nec feugiat nisl</li>
                <li>Nulla at volutpat dola</li>
                <li>Pharetra massa</li>
                <li>Massa ultricies mi</li>
              </ul>
              <div class="btn-wrap">
                <a href="#" class="btn-buy">Buy Now</a>
              </div>
            </div>
          </div>

          <div class="col-lg-3 col-md-6 mt-4 mt-lg-0">
            <div class="box" data-aos="zoom-in" data-aos-delay="400">
              <span class="advanced">Advanced</span>
              <h3>Ultimate</h3>
              <h4><sup>$</sup>49<span> / month</span></h4>
              <ul>
                <li>Aida dere</li>
                <li>Nec feugiat nisl</li>
                <li>Nulla at volutpat dola</li>
                <li>Pharetra massa</li>
                <li>Massa ultricies mi</li>
              </ul>
              <div class="btn-wrap">
                <a href="#" class="btn-buy">Buy Now</a>
              </div>
            </div>
          </div>

        </div>

      </div>
    </section>End Pricing Section -->

    <!-- ======= F.A.Q Section ======= -->
    <section id="faq" class="faq section-bg">
      <div class="container">

        <div class="section-title" data-aos="fade-up">
          <h2>F.A.Q</h2>
          <p>Frequently Asked Questions</p>
        </div>

        <div class="faq-list">
          <ul>
            <li data-aos="fade-up">
              <i class="bx bx-help-circle icon-help"></i> <a data-bs-toggle="collapse" class="collapse" data-bs-target="#faq-list-1">Apa kurikulum yang digunakan di SDIT Al Fatah? <i class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i></a>
              <div id="faq-list-1" class="collapse show" data-bs-parent=".faq-list">
                <p>
                  SDIT Al Fatah menggunakan kurikulum nasional yang diperkaya dengan pendidikan agama Islam terintegrasi. Kami juga menerapkan metode pembelajaran aktif dan kreatif yang berfokus pada pengembangan karakter dan potensi siswa.
                </p>
              </div>
            </li>

            <li data-aos="fade-up" data-aos-delay="100">
              <i class="bx bx-help-circle icon-help"></i> <a data-bs-toggle="collapse" data-bs-target="#faq-list-2" class="collapsed">Apa saja fasilitas yang tersedia di SDIT Al Fatah?<i class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i></a>
              <div id="faq-list-2" class="collapse" data-bs-parent=".faq-list">
                <p>
                  SDIT Al Fatah menyediakan berbagai fasilitas untuk mendukung proses belajar mengajar, termasuk ruang kelas yang nyaman, perpustakaan, laboratorium komputer, ruang ibadah, lapangan olahraga, dan ruang kegiatan ekstrakurikuler. Kami juga memiliki lingkungan sekolah yang aman dan ramah anak.
                </p>
              </div>
            </li>

            <li data-aos="fade-up" data-aos-delay="200">
              <i class="bx bx-help-circle icon-help"></i> <a data-bs-toggle="collapse" data-bs-target="#faq-list-3" class="collapsed">Apa saja fasilitas yang tersedia di SDIT Al Fatah? <i class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i></a>
              <div id="faq-list-3" class="collapse" data-bs-parent=".faq-list">
                <p>
                  SDIT Al Fatah menyediakan berbagai fasilitas untuk mendukung proses belajar mengajar, termasuk ruang kelas yang nyaman, perpustakaan, laboratorium komputer, ruang ibadah, lapangan olahraga, dan ruang kegiatan ekstrakurikuler. Kami juga memiliki lingkungan sekolah yang aman dan ramah anak.
                </p>
              </div>
            </li>

            <li data-aos="fade-up" data-aos-delay="300">
              <i class="bx bx-help-circle icon-help"></i> <a data-bs-toggle="collapse" data-bs-target="#faq-list-4" class="collapsed">Bagaimana sistem pengajaran agama di SDIT Al Fatah? <i class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i></a>
              <div id="faq-list-4" class="collapse" data-bs-parent=".faq-list">
                <p>
                  Di SDIT Al Fatah, pendidikan agama Islam menjadi bagian integral dari kurikulum. Selain pelajaran agama di kelas, siswa juga mengikuti kegiatan keagamaan seperti shalat berjamaah, hafalan Al-Qur'an, dan kegiatan keagamaan lainnya. Kami berkomitmen untuk menanamkan nilai-nilai Islami dalam kehidupan sehari-hari siswa
                </p>
              </div>
            </li>

            <li data-aos="fade-up" data-aos-delay="400">
              <i class="bx bx-help-circle icon-help"></i> <a data-bs-toggle="collapse" data-bs-target="#faq-list-5" class="collapsed">Apakah SDIT Al Fatah memiliki program ekstrakurikuler? <i class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i></a>
              <div id="faq-list-5" class="collapse" data-bs-parent=".faq-list">
                <p>
                  Ya, SDIT Al Fatah menawarkan berbagai program ekstrakurikuler untuk mengembangkan bakat dan minat siswa. Program ini meliputi olahraga, seni, sains, pramuka, dan kegiatan lainnya yang dirancang untuk memperkaya pengalaman belajar siswa di luar kelas.
                </p>
              </div>
            </li>

          </ul>
        </div>

      </div>
    </section><!-- End F.A.Q Section -->

    <!-- ======= Contact Section ======= -->
    <section id="contact" class="contact">
      <div class="container">

        <div class="section-title" data-aos="fade-up">
          <h2>Contact</h2>
          <p>Contact Us</p>
        </div>

        <div class="row">

          <div class="col-lg-4" data-aos="fade-right" data-aos-delay="100">
            <div class="info">
              <div class="address">
                <i class="bi bi-geo-alt"></i>
                <h4>Lokasi:</h4>
                <p>Jl. Masjid Al-Muawanah No.60, RT.006/RW.012, Aren Jaya, Kec. Bekasi Tim., Kota Bks, Jawa Barat 17111</p>
              </div>

              <div class="email">
                <i class="bi bi-envelope"></i>
                <h4>Email:</h4>
                <p>sditalfatah.60@gmail.com</p>
              </div>

              <div class="phone">
                <i class="bi bi-phone"></i>
                <h4>Telepon:</h4>
                <p>0821-22229862</p>
              </div>

            </div>

          </div>

          <div class="col-lg-8 mt-5 mt-lg-0" data-aos="fade-left" data-aos-delay="200">
            <form action="forms/contact.php" method="post" role="form" class="php-email-form">
              <div class="row">
                <div class="col-md-6 form-group">
                  <input type="text" name="name" class="form-control" id="name" placeholder="Your Name" required>
                </div>
                <div class="col-md-6 form-group mt-3 mt-md-0">
                  <input type="email" class="form-control" name="email" id="email" placeholder="Your Email" required>
                </div>
              </div>
              <div class="form-group mt-3">
                <input type="text" class="form-control" name="subject" id="subject" placeholder="Subject" required>
              </div>
              <div class="form-group mt-3">
                <textarea class="form-control" name="message" rows="5" placeholder="Message" required></textarea>
              </div>
              <div class="my-3">
                <div class="loading">Loading</div>
                <div class="error-message"></div>
                <div class="sent-message">Pesan Kamu Telah Terkirim. Terima Kasih!</div>
              </div>
              <div class="text-center"><button type="submit">Kirim Pesan</button></div>
            </form>

          </div>

        </div>

      </div>
    </section><!-- End Contact Section -->

  </main><!-- End #main -->

  <?php include __DIR__ . '/includes/public_footer.php'; ?>

  <?php include __DIR__ . '/includes/public_wa_float.php'; ?>
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js?v=1.1"></script>

</body>

</html>