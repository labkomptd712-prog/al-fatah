<?php
/** Tombol WhatsApp floating — nomor & pesan dari settings. */
if (trim($settings['wa_number'] ?? '') !== ''):
    $wa_href = alfatah_wa_url($settings);
?>
  <a href="<?= htmlspecialchars($wa_href) ?>" class="whatsapp-float d-flex align-items-center justify-content-center" target="_blank" rel="noopener noreferrer" aria-label="Chat WhatsApp">
    <i class="fa-brands fa-whatsapp"></i>
  </a>
<?php endif; ?>
