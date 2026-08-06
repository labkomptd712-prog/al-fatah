<?php
/** Icon media sosial footer dari tabel settings. */
$fb = trim($settings['fb_link'] ?? '');
$ig = trim($settings['ig_link'] ?? '');
$yt = trim($settings['yt_link'] ?? '');
$tt = trim($settings['tiktok_link'] ?? '');
$wa = alfatah_wa_url($settings);
?>
              <div class="social-links mt-3">
                <?php if ($fb !== ''): ?>
                <a href="<?= htmlspecialchars($fb) ?>" class="facebook" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                <?php endif; ?>
                <?php if ($ig !== ''): ?>
                <a href="<?= htmlspecialchars($ig) ?>" class="instagram" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                <?php endif; ?>
                <?php if ($yt !== ''): ?>
                <a href="<?= htmlspecialchars($yt) ?>" class="youtube" target="_blank" rel="noopener noreferrer" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
                <?php endif; ?>
                <?php if ($tt !== ''): ?>
                <a href="<?= htmlspecialchars($tt) ?>" class="tiktok" target="_blank" rel="noopener noreferrer" aria-label="TikTok"><i class="fa-brands fa-tiktok"></i></a>
                <?php endif; ?>
                <?php if (($settings['wa_number'] ?? '') !== ''): ?>
                <a href="<?= htmlspecialchars($wa) ?>" class="whatsapp" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
                <?php endif; ?>
              </div>
