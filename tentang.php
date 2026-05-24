<?php
require_once 'config/koneksi.php';
$judul  = 'Tentang Kami';
$db     = koneksi();
$dokter = $db->query("SELECT DISTINCT dokter, nama_poli FROM poli WHERE status='aktif' ORDER BY id")->fetch_all(MYSQLI_ASSOC);
require_once 'includes/header.php';
?>

<div class="halaman-container">
  <div class="container">

    <!-- ===== HERO TENTANG ===== -->
    <div class="tentang-hero">
      <span class="label-pil">Tentang Kami</span>
      <h1>Puskesmas Sehat Sejahtera</h1>
      <p>Pusat Kesehatan Masyarakat yang berdedikasi melayani seluruh lapisan masyarakat
         dengan pelayanan kesehatan komprehensif, terjangkau, dan bermutu tinggi.</p>
    </div>

    <!-- ===== PROFIL SINGKAT ===== -->
    <div class="tentang-grid-2">
      <div class="tentang-gambar">
        <img src="https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?w=700&h=520&fit=crop"
             alt="Gedung Puskesmas" style="width:100%;border-radius:16px;object-fit:cover;max-height:360px;">
        <div class="tentang-badge-foto">
          <span>&#127942;</span>
          <div>
            <strong>Akreditasi Paripurna</strong>
            <small>Kementerian Kesehatan RI — 2023</small>
          </div>
        </div>
      </div>
      <div class="tentang-deskripsi">
        <h2>Melayani Masyarakat dengan Sepenuh Hati</h2>
        <p>Puskesmas Sehat Sejahtera berdiri sejak tahun <strong>1985</strong> dan telah melayani
           ratusan ribu warga selama lebih dari tiga dekade. Kami berkomitmen menghadirkan
           layanan kesehatan primer yang berkualitas, mudah diakses, dan berorientasi pada
           kepuasan pasien.</p>
        <p>Dengan sistem informasi digital modern, masyarakat dapat mendaftar poli secara online,
           memantau status antrian, dan mengakses informasi kesehatan — kapan saja dan di mana saja.</p>
        <p>Kami terus berinovasi demi meningkatkan mutu pelayanan dan mewujudkan masyarakat yang
           sehat, mandiri, dan sejahtera.</p>
        <div class="tentang-stat-baris">
          <div class="tentang-stat"><strong>40+</strong><span>Tahun Berdiri</span></div>
          <div class="tentang-stat"><strong>12.450+</strong><span>Pasien Terlayani</span></div>
          <div class="tentang-stat"><strong>6</strong><span>Poli Aktif</span></div>
          <div class="tentang-stat"><strong>24</strong><span>Tenaga Medis</span></div>
        </div>
      </div>
    </div>

    <!-- ===== VISI & MISI ===== -->
    <div class="bagian-header" style="margin-top:60px">
      <span class="label-pil">Arah & Tujuan</span>
      <h2>Visi &amp; Misi</h2>
    </div>
    <div class="tentang-grid-2 tentang-visi-misi">
      <div class="visi-kartu">
        <div class="vm-ikon">&#128301;</div>
        <h3>Visi</h3>
        <p>Menjadi Pusat Kesehatan Masyarakat yang terdepan dalam memberikan pelayanan
           kesehatan prima, profesional, dan berorientasi pada masyarakat demi terwujudnya
           derajat kesehatan masyarakat yang optimal.</p>
      </div>
      <div class="misi-kartu">
        <div class="vm-ikon">&#127919;</div>
        <h3>Misi</h3>
        <ul class="misi-list">
          <li>Menyelenggarakan pelayanan kesehatan dasar yang komprehensif dan bermutu.</li>
          <li>Meningkatkan profesionalisme tenaga kesehatan melalui pelatihan berkala.</li>
          <li>Memberdayakan masyarakat untuk hidup sehat dan mandiri.</li>
          <li>Mengembangkan sistem informasi kesehatan yang modern dan terintegrasi.</li>
          <li>Menjalin kemitraan dengan lintas sektor untuk mendukung pembangunan kesehatan.</li>
        </ul>
      </div>
    </div>

    <!-- ===== KEUNGGULAN ===== -->
    <div class="bagian-header" style="margin-top:60px">
      <span class="label-pil">Mengapa Pilih Kami</span>
      <h2>Keunggulan Kami</h2>
    </div>
    <div class="keunggulan-grid">
      <?php
      $keunggulan = [
        ['&#127942;', 'Terakreditasi Nasional',    'Mendapat akreditasi Paripurna dari Kementerian Kesehatan RI sebagai bukti mutu pelayanan.'],
        ['&#128100;', 'Tenaga Medis Profesional',  'Dokter umum, dokter gigi, bidan, perawat, dan tenaga kesehatan berpengalaman & bersertifikat.'],
        ['&#11088;',  'Pelayanan Prima',            'Berkomitmen memberikan pelayanan cepat, ramah, dan transparan kepada setiap pasien.'],
        ['&#127973;', 'Fasilitas Lengkap',          'Laboratorium klinik, apotek, ruang tindakan, poli gigi, KIA/KB, dan fasilitas modern lainnya.'],
        ['&#128241;', 'Pendaftaran Digital',        'Sistem pendaftaran online 24 jam — tidak perlu antri panjang di loket.'],
        ['&#127968;', 'Lokasi Strategis',           'Berada di pusat kota, mudah dijangkau kendaraan umum maupun pribadi dari berbagai penjuru.'],
      ];
      foreach ($keunggulan as $k): ?>
      <div class="keunggulan-kartu">
        <div class="keunggulan-ikon"><?php echo $k[0]; ?></div>
        <h4><?php echo $k[1]; ?></h4>
        <p><?php echo $k[2]; ?></p>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- ===== TIM DOKTER ===== -->
    <div class="bagian-header" style="margin-top:60px">
      <span class="label-pil">Tenaga Medis</span>
      <h2>Tim Dokter &amp; Tenaga Kesehatan</h2>
    </div>
    <div class="dokter-grid">
      <?php foreach ($dokter as $d): ?>
      <div class="dokter-kartu">
        <div class="dokter-avatar"><?php echo strtoupper(substr($d['dokter'],0,1)); ?></div>
        <h4><?php echo htmlspecialchars($d['dokter']); ?></h4>
        <span class="dokter-poli"><?php echo htmlspecialchars($d['nama_poli']); ?></span>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- ===== KONTAK & LOKASI ===== -->
    <div class="bagian-header" style="margin-top:60px">
      <span class="label-pil">Hubungi Kami</span>
      <h2>Informasi Kontak</h2>
    </div>
    <div class="kontak-grid">
      <div class="kontak-info">
        <div class="kontak-baris"><span class="kontak-ikon">&#128205;</span><div><strong>Alamat</strong><p>Jl. Kesehatan No. 1, Kec. Sehat, Kota Sejahtera</p></div></div>
        <div class="kontak-baris"><span class="kontak-ikon">&#128222;</span><div><strong>Telepon</strong><p>(021) 123-4567</p></div></div>
        <div class="kontak-baris"><span class="kontak-ikon">&#128140;</span><div><strong>Email</strong><p>info@puskesmas-sehat.go.id</p></div></div>
        <div class="kontak-baris"><span class="kontak-ikon">&#128336;</span><div><strong>Jam Operasional</strong><p>Senin – Jumat: 08:00 – 15:00<br>Sabtu: 08:00 – 12:00<br>Minggu &amp; Hari Libur: <em>Tutup</em></p></div></div>
        <a href="pendaftaran.php" class="tombol tombol-utama" style="margin-top:20px;display:inline-block">
          &#128197; Daftar Online Sekarang
        </a>
      </div>
      <div class="kontak-peta">
        <!-- Ganti src di bawah dengan embed Google Maps lokasi asli Puskesmas Anda -->
        <iframe
          src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.521260322283!2d106.8195613!3d-6.1944491!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f5390917b759%3A0x6b45e67356080477!2sMonumen%20Nasional!5e0!3m2!1sid!2sid!4v1699999999999!5m2!1sid!2sid"
          width="100%" height="100%" style="border:0;border-radius:12px;min-height:280px;"
          allowfullscreen="" loading="lazy">
        </iframe>
      </div>
    </div>

  </div>
</div>

<?php require_once 'includes/footer.php'; ?>