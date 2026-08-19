<?php
/** Navbar publik — set $nav_home = 'index.php' atau '#hero' sesuai halaman. */
$nav_home = $nav_home ?? 'index.php';
$is_home = !empty($is_home);
$is_berita = !empty($is_berita);
?>
      <nav id="navbar" class="navbar">
        <ul>
          <li><a class="nav-link scrollto<?= $is_home ? ' active' : '' ?>" href="<?= $is_home ? '#hero' : 'index.php#hero' ?>">Beranda</a></li>
          <li class="dropdown"><a href="#"><span>Profil</span> <i class="bi bi-chevron-down"></i></a>
            <ul class="dropdown-profil">
              <li><a href="sejarah.php">Sejarah</a></li>
              <li><a href="visi-misi.php">Visi &amp; Misi</a></li>
              <li><a href="struktur.php">Struktur Organisasi</a></li>
            </ul>
          </li>
          <li><a class="nav-link<?= $is_berita ? ' active' : '' ?>" href="berita.php">Berita</a></li>
          <li><a class="nav-link<?= (strpos($_SERVER['SCRIPT_NAME'], '/fasilitas.php') !== false || strpos($_SERVER['SCRIPT_NAME'], '/fasilitas-kategori.php') !== false || strpos($_SERVER['SCRIPT_NAME'], '/fasilitas-detail.php') !== false) ? ' active' : '' ?>" href="fasilitas.php">Facility</a></li>
          <li class="dropdown"><a href="#" class="<?= (strpos($_SERVER['SCRIPT_NAME'], '/galeri.php') !== false || strpos($_SERVER['SCRIPT_NAME'], '/galeri-kategori.php') !== false || strpos($_SERVER['SCRIPT_NAME'], '/ekskul.php') !== false || strpos($_SERVER['SCRIPT_NAME'], '/ekskul-kategori.php') !== false) ? 'active' : '' ?>"><span>Gallery</span> <i class="bi bi-chevron-down"></i></a>
            <ul>
              <li><a href="galeri.php" class="<?= (strpos($_SERVER['SCRIPT_NAME'], '/galeri.php') !== false || strpos($_SERVER['SCRIPT_NAME'], '/galeri-kategori.php') !== false) ? 'active' : '' ?>">Galeri Foto</a></li>
              <li><a href="ekskul.php" class="<?= (strpos($_SERVER['SCRIPT_NAME'], '/ekskul.php') !== false || strpos($_SERVER['SCRIPT_NAME'], '/ekskul-kategori.php') !== false) ? 'active' : '' ?>">Ekskul</a></li>
            </ul>
          </li>
          <li><a class="nav-link<?= (strpos($_SERVER['SCRIPT_NAME'], '/prestasi.php') !== false || strpos($_SERVER['SCRIPT_NAME'], '/prestasi-kategori.php') !== false) ? ' active' : '' ?>" href="prestasi.php">Prestasi</a></li>
          <li><a class="nav-link<?= (strpos($_SERVER['SCRIPT_NAME'], '/team.php') !== false) ? ' active' : '' ?>" href="team.php">Team</a></li>
          <li><a class="nav-link<?= (strpos($_SERVER['SCRIPT_NAME'], '/contact.php') !== false) ? ' active' : '' ?>" href="contact.php">Contact</a></li>
        </ul>
        <i class="bi bi-list mobile-nav-toggle"></i>
      </nav><!-- .navbar -->
