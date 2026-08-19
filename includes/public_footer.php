<?php
$phone = trim($settings['phone_number'] ?? '');
$wa = trim($settings['wa_number'] ?? '');

$phone_display = format_phone_display($phone);
$wa_display = format_phone_display($wa);

if ($phone_display === '' && $wa_display === '') {
    $contact_display = '<strong>Telepon/WhatsApp:</strong> 0821 2222 9862';
} elseif ($phone_display === $wa_display || $wa_display === '') {
    $contact_display = '<strong>Telepon/WhatsApp:</strong> ' . htmlspecialchars($phone_display);
} else {
    $contact_display = '<strong>Telepon:</strong> ' . htmlspecialchars($phone_display) . '<br><strong>WhatsApp:</strong> ' . htmlspecialchars($wa_display);
}

// Fetch dynamic footer links
try {
    $stmtKepegawaian = $pdo->prepare("SELECT * FROM footer_links WHERE category = 'layanan_kepegawaian' ORDER BY display_order ASC");
    $stmtKepegawaian->execute();
    $linksKepegawaian = $stmtKepegawaian->fetchAll();
} catch (PDOException $e) {
    $linksKepegawaian = [];
}

try {
    $stmtTautan = $pdo->prepare("SELECT * FROM footer_links WHERE category = 'tautan' ORDER BY display_order ASC");
    $stmtTautan->execute();
    $linksTautan = $stmtTautan->fetchAll();
} catch (PDOException $e) {
    $linksTautan = [];
}
?>
<!-- ======= Footer ======= -->
<footer id="footer">
  <div class="footer-top">
    <div class="container">
      <div class="row">

        <!-- Kolom 1 (Kanan pada desktop, Paling Atas pada mobile) -->
        <div class="col-lg-4 col-md-12 order-1 order-lg-3 mb-4 mb-lg-0">
          <div class="footer-info">
            <h3>SDIT AL FATAH</h3>
            <p class="pb-3"><em>"Bersama Mencetak Generasi Islami yang Cerdas dan Berakhlak Mulia"</em></p>
            <p>
              Jl. Masjid Al-Muawanah No.60, RT.006/RW.012, Aren Jaya, Kec. Bekasi Tim., Kota Bks, Jawa Barat 17111 <br>
              <br>
              <?= $contact_display ?><br>
              <strong>Email:</strong> sditalfatah.60@gmail.com<br>
            </p>
            <?php include __DIR__ . '/public_footer_social.php'; ?>
          </div>
        </div>

        <!-- Kolom 2: Jam Pelayanan (Hanya muncul di Mobile, posisi kedua) -->
        <div class="col-lg-4 col-md-6 order-2 d-lg-none footer-links mb-4">
          <h4>Jam Pelayanan</h4>
          <div class="jam-pelayanan-card">
            <div class="jam-pelayanan-item">
              <div class="icon-wrapper">
                <i class="fa-solid fa-clock"></i>
              </div>
              <span class="jam-pelayanan-text">Senin–Jumat: 07.00–16.00 WIB</span>
            </div>
            <div class="jam-pelayanan-item">
              <div class="icon-wrapper">
                <i class="fa-solid fa-calendar-xmark"></i>
              </div>
              <span class="jam-pelayanan-text">Sabtu, Minggu & Hari Besar: Libur</span>
            </div>
          </div>
          <div class="mt-3">
            <div class="waktu-sekarang-card">
              <div class="waktu-sekarang-label">
                <span class="live-dot"></span>
                Waktu Sekarang
              </div>
              <div class="realtime-clock waktu-sekarang-jam">00:00:00 WIB</div>
              <div class="realtime-date waktu-sekarang-tanggal">-</div>
            </div>
          </div>
        </div>

        <!-- Kolom 3: Layanan Kepegawaian (Kiri Atas pada desktop, Ketiga pada mobile) -->
        <div class="col-lg-4 col-md-6 order-3 order-lg-1 footer-links">
          <h4 class="footer-collapse-header" data-bs-toggle="collapse" data-bs-target="#collapseKepegawaian" role="button" aria-expanded="false" aria-controls="collapseKepegawaian">
            Layanan Kepegawaian
            <i class="bx bx-chevron-down d-lg-none"></i>
          </h4>
          <div class="collapse d-lg-block" id="collapseKepegawaian">
            <ul>
              <?php if (empty($linksKepegawaian)): ?>
                <li><span class="text-white-50" style="opacity: 0.6;">Belum ada menu kepegawaian.</span></li>
              <?php else: ?>
                <?php foreach ($linksKepegawaian as $link): ?>
                  <li>
                    <i class="bx bx-chevron-right"></i>
                    <?php if (!empty($link['file_path'])): ?>
                      <a href="admin/uploads/footer-docs/<?= htmlspecialchars($link['file_path']) ?>" target="_blank"><?= htmlspecialchars($link['title']) ?></a>
                    <?php elseif (!empty($link['external_url'])): ?>
                      <a href="<?= htmlspecialchars($link['external_url']) ?>" target="_blank"><?= htmlspecialchars($link['title']) ?></a>
                    <?php else: ?>
                      <span class="text-white-50" style="opacity: 0.6; cursor: default; font-size: 15px;"><?= htmlspecialchars($link['title']) ?> <span class="small" style="font-size: 10px; font-style: italic;">(belum tersedia)</span></span>
                    <?php endif; ?>
                  </li>
                <?php endforeach; ?>
              <?php endif; ?>
            </ul>
          </div>
          
          <!-- Jam Pelayanan (Hanya muncul di Desktop, berada langsung di bawah Layanan Kepegawaian) -->
          <div class="d-none d-lg-block mt-4">
            <h4>Jam Pelayanan</h4>
            <div class="jam-pelayanan-card">
              <div class="jam-pelayanan-item">
                <div class="icon-wrapper">
                  <i class="fa-solid fa-clock"></i>
                </div>
                <span class="jam-pelayanan-text">Senin–Jumat: 07.00–16.00 WIB</span>
              </div>
              <div class="jam-pelayanan-item">
                <div class="icon-wrapper">
                  <i class="fa-solid fa-calendar-xmark"></i>
                </div>
                <span class="jam-pelayanan-text">Sabtu, Minggu & Hari Besar: Libur</span>
              </div>
            </div>
            <div class="mt-3">
              <div class="waktu-sekarang-card">
                <div class="waktu-sekarang-label">
                  <span class="live-dot"></span>
                  Waktu Sekarang
                </div>
                <div class="realtime-clock waktu-sekarang-jam">00:00:00 WIB</div>
                <div class="realtime-date waktu-sekarang-tanggal">-</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Kolom 4 (Tengah pada desktop, Keempat pada mobile) -->
        <div class="col-lg-4 col-md-6 order-4 order-lg-2 footer-links">
          <h4 class="footer-collapse-header" data-bs-toggle="collapse" data-bs-target="#collapseTautan" role="button" aria-expanded="false" aria-controls="collapseTautan">
            Tautan
            <i class="bx bx-chevron-down d-lg-none"></i>
          </h4>
          <div class="collapse d-lg-block" id="collapseTautan">
            <ul>
              <?php if (empty($linksTautan)): ?>
                <li><span class="text-white-50" style="opacity: 0.6;">Belum ada tautan.</span></li>
              <?php else: ?>
                <?php foreach ($linksTautan as $link): ?>
                  <li>
                    <i class="bx bx-chevron-right"></i>
                    <?php if (!empty($link['file_path'])): ?>
                      <a href="admin/uploads/footer-docs/<?= htmlspecialchars($link['file_path']) ?>" target="_blank"><?= htmlspecialchars($link['title']) ?></a>
                    <?php elseif (!empty($link['external_url'])): ?>
                      <a href="<?= htmlspecialchars($link['external_url']) ?>" target="_blank"><?= htmlspecialchars($link['title']) ?></a>
                    <?php else: ?>
                      <span class="text-white-50" style="opacity: 0.6; cursor: default; font-size: 15px;"><?= htmlspecialchars($link['title']) ?> <span class="small" style="font-size: 10px; font-style: italic;">(belum tersedia)</span></span>
                    <?php endif; ?>
                  </li>
                <?php endforeach; ?>
              <?php endif; ?>
            </ul>
          </div>
        </div>

      </div>
    </div>
  </div>

  <div class="container">
    <div class="copyright">
      &copy; Copyright <strong><span style="color: #0fff63;">Sdit Al Fatah</span></strong>. All Rights Reserved
    </div>
    <div class="credits">
      Created & Designed by <strong><span style="color: #00ff59;">Adriansyah Dwi A</span></strong>
    </div>
  </div>
</footer><!-- End Footer -->

<style>
@import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@700;800&display=swap');

@media (max-width: 991.98px) {
    .footer-collapse-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        padding-bottom: 10px;
        margin-bottom: 10px;
        cursor: pointer;
    }
    .footer-collapse-header i {
        transition: transform 0.3s ease;
    }
    .footer-collapse-header[aria-expanded="true"] i {
        transform: rotate(180deg);
    }
    .footer-links .collapse:not(.show) {
        display: none !important; /* Force hide in mobile when not expanded */
    }
}
@media (min-width: 992px) {
    .footer-collapse-header {
        pointer-events: none;
        cursor: default;
    }
    .footer-collapse-header i {
        display: none !important;
    }
}

/* Card Jam Pelayanan */
.jam-pelayanan-card {
  background: rgba(255, 255, 255, 0.04);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 14px;
  padding: 20px;
  backdrop-filter: blur(8px);
}
.jam-pelayanan-item {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 10px 0;
}
.jam-pelayanan-item:not(:last-child) {
  border-bottom: 1px solid rgba(255, 255, 255, 0.06);
}
.jam-pelayanan-item .icon-wrapper {
  width: 38px;
  height: 38px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  font-size: 16px;
}
.jam-pelayanan-item:nth-child(1) .icon-wrapper {
  background: rgba(34, 197, 94, 0.15);
  color: #4ade80;
}
.jam-pelayanan-item:nth-child(2) .icon-wrapper {
  background: rgba(239, 68, 68, 0.15);
  color: #f87171;
}
.jam-pelayanan-text {
  font-size: 15px;
  font-weight: 500;
  color: rgba(255,255,255,0.9);
  line-height: 1.5;
  font-family: 'Poppins', sans-serif;
}

/* Card Waktu Sekarang */
.waktu-sekarang-card {
  background: linear-gradient(135deg, rgba(34, 197, 94, 0.12), rgba(20, 20, 50, 0.3));
  border: 1px solid rgba(34, 197, 94, 0.25);
  border-radius: 14px;
  padding: 22px;
  position: relative;
  overflow: hidden;
  box-sizing: border-box;
}
.waktu-sekarang-card::before {
  content: '';
  position: absolute;
  top: -30%;
  right: -10%;
  width: 120px;
  height: 120px;
  background: radial-gradient(circle, rgba(74, 222, 128, 0.15), transparent 70%);
  border-radius: 50%;
  pointer-events: none;
}
.waktu-sekarang-label {
  font-size: 12px;
  font-weight: 700;
  letter-spacing: 1.5px;
  text-transform: uppercase;
  color: rgba(74, 222, 128, 0.8);
  margin-bottom: 10px;
  display: flex;
  align-items: center;
  gap: 8px;
  font-family: 'Poppins', sans-serif;
}
.waktu-sekarang-jam {
  font-family: 'JetBrains Mono', 'Roboto Mono', 'Consolas', monospace;
  font-weight: 800; /* extra bold */
  font-size: 34px;
  letter-spacing: 1px;
  color: #4ade80;
  text-shadow: 0 0 20px rgba(74, 222, 128, 0.4);
  -webkit-text-stroke: 0.5px rgba(74, 222, 128, 0.3); /* tambahan ketebalan visual */
  line-height: 1.2;
}
.waktu-sekarang-tanggal {
  font-size: 13px;
  color: rgba(255, 255, 255, 0.55);
  margin-top: 6px;
  font-family: 'Poppins', sans-serif;
}

/* Blinking Live Indicator Dot */
.live-dot {
  width: 8px;
  height: 8px;
  background-color: #4ade80;
  border-radius: 50%;
  display: inline-block;
  animation: pulse 1.5s infinite ease-in-out;
  box-shadow: 0 0 8px #4ade80;
}

@keyframes pulse {
  0% {
    opacity: 1;
    transform: scale(1);
  }
  50% {
    opacity: 0.3;
    transform: scale(0.9);
  }
  100% {
    opacity: 1;
    transform: scale(1);
  }
}

/* Responsive adjustments for mobile footer layout */
@media (max-width: 767.98px) {
  .jam-pelayanan-card {
    padding: 16px;
  }
  .jam-pelayanan-text {
    font-size: 13px;
  }
  .waktu-sekarang-card {
    padding: 16px;
  }
  .waktu-sekarang-jam {
    font-size: 26px;
  }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function updateClock() {
        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        
        const clientDate = new Date();
        const utc = clientDate.getTime() + (clientDate.getTimezoneOffset() * 60000);
        const wibDate = new Date(utc + (3600000 * 7)); // WIB (UTC+7)
        
        const dayName = days[wibDate.getDay()];
        const dateNum = String(wibDate.getDate()).padStart(2, '0');
        const monthName = months[wibDate.getMonth()];
        const year = wibDate.getFullYear();
        
        const hours = String(wibDate.getHours()).padStart(2, '0');
        const minutes = String(wibDate.getMinutes()).padStart(2, '0');
        const seconds = String(wibDate.getSeconds()).padStart(2, '0');
        
        const dateString = `${dayName}, ${dateNum} ${monthName} ${year}`;
        const timeString = `${hours}:${minutes}:${seconds} WIB`;
        
        document.querySelectorAll('.realtime-clock').forEach(el => el.textContent = timeString);
        document.querySelectorAll('.realtime-date').forEach(el => el.textContent = dateString);
    }
    
    updateClock();
    setInterval(updateClock, 1000);
});
</script>
