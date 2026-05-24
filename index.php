<?php
require_once 'config/koneksi.php';
$judul   = 'Beranda';
$db      = koneksi();
$poliList    = $db->query("SELECT * FROM poli WHERE status='aktif' ORDER BY id")->fetch_all(MYSQLI_ASSOC);
$totalDaftar = $db->query("SELECT COUNT(*) AS c FROM pendaftaran")->fetch_assoc()['c'];
require_once 'includes/header.php';
?>

<section class="hero">
  <div class="container hero-dalam">
    <div class="hero-konten">
      <span class="label-pil">Sistem Informasi Puskesmas</span>
      <h1>Layanan Kesehatan <span class="warna-utama">Terpadu</span></h1>
      <p>Daftar poli online dengan mudah. Tidak perlu antri panjang.</p>
      <div class="hero-tombol">
        <a href="pendaftaran.php" class="tombol tombol-utama">Daftar Poli Online &rarr;</a>
        <a href="layanan.php"     class="tombol tombol-outline">Lihat Layanan</a>
      </div>
      <div class="hero-statistik">
        <div class="statistik-item"><strong><?php echo number_format($totalDaftar); ?>+</strong><span>Pasien Terlayani</span></div>
        <div class="statistik-item"><strong>&lt;15 Menit</strong><span>Waktu Tunggu</span></div>
        <div class="statistik-item"><strong><?php echo count($poliList); ?></strong><span>Poli Aktif</span></div>
      </div>
    </div>
    <div class="hero-gambar">
      <img src="https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=560&h=560&fit=crop" alt="Pelayanan">
    </div>
  </div>
</section>

<section class="bagian">
  <div class="container">
    <div class="bagian-header">
      <span class="label-pil">Alur Pendaftaran</span>
      <h2>6 Langkah Mudah Pendaftaran Online</h2>
    </div>
    <div class="alur-grid">
      <?php
      $langkah = [
        ['01','Buka Hotline Instansi','Akses website resmi Puskesmas.'],
        ['02','Pilih Layanan Poli',   'Pilih poli sesuai keluhan Anda.'],
        ['03','Isi Data Pasien',      'Lengkapi data identitas dengan benar.'],
        ['04','Pilih Tanggal',        'Pilih tanggal kunjungan.'],
        ['05','Pilih Sesi Waktu',     'Pilih jam kontrol yang tersedia.'],
        ['06','Konfirmasi Akhir',     'Cek data, klik Ya, simpan bukti.'],
      ];
      foreach ($langkah as $l): ?>
      <div class="alur-kartu">
        <div class="alur-nomor"><?php echo $l[0]; ?></div>
        <h3><?php echo $l[1]; ?></h3>
        <p><?php echo $l[2]; ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="bagian bagian-abu">
  <div class="container">
    <div class="bagian-header">
      <span class="label-pil">Layanan Kami</span>
      <h2>Poli yang Tersedia</h2>
    </div>
    <div class="poli-grid">
      <?php foreach ($poliList as $p): ?>
      <div class="poli-kartu">
        <h3><?php echo htmlspecialchars($p['nama_poli']); ?></h3>
        <p><?php echo htmlspecialchars($p['deskripsi']); ?></p>
        <div class="poli-info">
          <span>&#128336; <?php echo htmlspecialchars($p['jam_operasional']); ?></span>
          <span>&#128100; <?php echo htmlspecialchars($p['dokter']); ?></span>
        </div>
        <a href="pendaftaran.php?poli=<?php echo $p['id']; ?>" class="tombol tombol-utama tombol-kecil">Daftar</a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>