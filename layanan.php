<?php
require_once 'config/koneksi.php';
$judul = 'Layanan Poli';
$db    = koneksi();
$poli  = $db->query("SELECT * FROM poli WHERE status='aktif' ORDER BY id")->fetch_all(MYSQLI_ASSOC);
require_once 'includes/header.php';
?>
<div class="halaman-container">
  <div class="container">
    <div class="bagian-header">
      <span class="label-pil">Layanan Kami</span>
      <h1>Layanan Poli Tersedia</h1>
      <p>Pilih poli sesuai kebutuhan kesehatan Anda</p>
    </div>
    <div class="poli-grid poli-grid-besar">
      <?php foreach ($poli as $p): ?>
      <div class="poli-kartu">
        <h3><?php echo htmlspecialchars($p['nama_poli']); ?></h3>
        <p><?php echo htmlspecialchars($p['deskripsi']); ?></p>
        <hr style="margin:12px 0;border-color:#d4ead9">
        <table class="info-tabel">
          <tr><td>&#128100; Dokter</td><td><?php echo htmlspecialchars($p['dokter']); ?></td></tr>
          <tr><td>&#128197; Jadwal</td><td><?php echo htmlspecialchars($p['jadwal']); ?></td></tr>
          <tr><td>&#128336; Jam</td>   <td><?php echo htmlspecialchars($p['jam_operasional']); ?></td></tr>
        </table>
        <a href="pendaftaran.php?poli=<?php echo $p['id']; ?>"
           class="tombol tombol-utama" style="display:block;text-align:center;margin-top:12px">
          Daftar Sekarang
        </a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php require_once 'includes/footer.php'; ?>