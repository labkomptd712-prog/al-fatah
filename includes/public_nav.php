<?php
/** Navbar publik — set $nav_home = 'index.php' atau '#hero' sesuai halaman. */
$nav_home = $nav_home ?? 'index.php';
$is_home = !empty($is_home);
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
          <li><a class="nav-link scrollto" href="<?= $is_home ? '#about' : 'index.php#about' ?>">About</a></li>
          <li><a class="nav-link scrollto" href="<?= $is_home ? '#features' : 'index.php#features' ?>">Facility</a></li>
          <li><a class="nav-link scrollto" href="<?= $is_home ? '#gallery' : 'index.php#gallery' ?>">Gallery</a></li>
          <li><a class="nav-link scrollto" href="<?= $is_home ? '#team' : 'index.php#team' ?>">Team</a></li>
          <li><a class="nav-link scrollto" href="<?= $is_home ? '#contact' : 'index.php#contact' ?>">Contact</a></li>
        </ul>
        <i class="bi bi-list mobile-nav-toggle"></i>
      </nav><!-- .navbar -->
