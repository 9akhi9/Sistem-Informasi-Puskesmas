<?php
require_once 'config/koneksi.php';
$judul     = 'Pendaftaran Online';
$db        = koneksi();
$poliList  = $db->query("SELECT * FROM poli WHERE status='aktif' ORDER BY id")->fetch_all(MYSQLI_ASSOC);
$poliPilih = isset($_GET['poli']) ? (int)$_GET['poli'] : 0;
$sukses    = false;
$dataBukti = [];
$error     = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['konfirmasi'])) {
    $kode    = generateKode();
    $poliId  = (int)$_POST['poli_id'];
    $stmtAnt = $db->prepare("SELECT COUNT(*) AS c FROM pendaftaran WHERE poli_id=? AND tanggal_kunjungan=? AND sesi=?");
    $stmtAnt->bind_param('iss', $poliId, $_POST['tanggal_kunjungan'], $_POST['sesi']);
    $stmtAnt->execute();
    $antrian = $stmtAnt->get_result()->fetch_assoc()['c'] + 1;
    $stmt = $db->prepare("INSERT INTO pendaftaran
        (kode_daftar,nama_lengkap,nik,tanggal_lahir,jenis_kelamin,no_telepon,
         alamat,jenis_jaminan,no_jaminan,poli_id,tanggal_kunjungan,sesi,nomor_antrian)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param('sssssssssissi',
        $kode, $_POST['nama_lengkap'], $_POST['nik'], $_POST['tanggal_lahir'],
        $_POST['jenis_kelamin'], $_POST['no_telepon'], $_POST['alamat'],
        $_POST['jenis_jaminan'], $_POST['no_jaminan'],
        $poliId, $_POST['tanggal_kunjungan'], $_POST['sesi'], $antrian);
    if ($stmt->execute()) {
        $sukses = true;
        $namaPoli = '';
        foreach ($poliList as $pl) { if ($pl['id'] == $poliId) { $namaPoli = $pl['nama_poli']; break; } }
        $dataBukti = ['kode'=>$kode,'antrian'=>$antrian,'nama'=>$_POST['nama_lengkap'],
            'nik'=>$_POST['nik'],'poli'=>$namaPoli,'tanggal'=>$_POST['tanggal_kunjungan'],'sesi'=>$_POST['sesi']];
    } else { $error = 'Gagal menyimpan. Silakan coba lagi.'; }
}
$langkahAktif = isset($_POST['langkah']) ? (int)$_POST['langkah'] : 1;
require_once 'includes/header.php';
?>
<link rel="stylesheet" href="assets/css/pendaftaran.css">
<div class="halaman-container"><div class="container"><div class="form-daftar-wrapper">
<div class="form-daftar-header"><h1>Pendaftaran Poli Online</h1><p><?php echo APP_NAME; ?></p></div>

<?php if ($sukses): ?>
<div class="sukses-layar">
  <div class="sukses-ikon">&#10004;</div>
  <h2>Pendaftaran Berhasil!</h2>
  <div class="bukti-kartu" id="buktiCetak">
    <div class="bukti-header"><p>BUKTI PENDAFTARAN</p><strong><?php echo APP_NAME; ?></strong></div>
    <div class="bukti-isi">
      <div class="bukti-kode"><?php echo $dataBukti['kode']; ?></div>
      <div class="bukti-antrian">Nomor Antrian: <strong><?php echo $dataBukti['antrian']; ?></strong></div>
      <table class="bukti-tabel">
        <tr><td>Nama</td>   <td><?php echo htmlspecialchars($dataBukti['nama']); ?></td></tr>
        <tr><td>NIK</td>    <td><?php echo htmlspecialchars($dataBukti['nik']); ?></td></tr>
        <tr><td>Poli</td>   <td><?php echo htmlspecialchars($dataBukti['poli']); ?></td></tr>
        <tr><td>Tanggal</td><td><?php echo date('d F Y', strtotime($dataBukti['tanggal'])); ?></td></tr>
        <tr><td>Sesi</td>   <td><?php echo htmlspecialchars($dataBukti['sesi']); ?></td></tr>
      </table>
    </div>
  </div>
  <div class="sukses-aksi">
    <button onclick="window.print()" class="tombol tombol-outline">&#128438; Cetak</button>
    <a href="index.php" class="tombol tombol-utama">&#127968; Beranda</a>
  </div>
</div>
<?php else: ?>
<!-- Indikator langkah -->
<div class="indikator-langkah">
  <?php $label=['Hotline','Poli','Data','Jadwal','Sesi','Konfirmasi'];
  for($i=1;$i<=6;$i++): $k=$i<$langkahAktif?'selesai':($i==$langkahAktif?'aktif':''); ?>
  <div class="langkah-item <?php echo $k; ?>">
    <span class="langkah-bulat"><?php echo $i<$langkahAktif?'&#10003;':$i; ?></span>
    <small><?php echo $label[$i-1]; ?></small>
  </div>
  <?php if($i<6): ?><div class="langkah-garis <?php echo $i<$langkahAktif?'selesai':''; ?>"></div><?php endif; ?>
  <?php endfor; ?>
</div>

<?php if($error): ?><div class="pesan-error"><?php echo $error; ?></div><?php endif; ?>

<form method="POST" id="formDaftar" novalidate>
  <?php foreach($_POST as $k=>$v): if(!in_array($k,['langkah','konfirmasi'])): ?>
  <input type="hidden" name="<?php echo htmlspecialchars($k); ?>" value="<?php echo htmlspecialchars($v); ?>">
  <?php endif; endforeach; ?>
  <input type="hidden" name="langkah" id="inputLangkah" value="<?php echo $langkahAktif; ?>">

  <!-- LANGKAH 1 -->
  <div class="kotak-langkah" id="kotak1" style="<?php echo $langkahAktif!=1?'display:none':''; ?>">
    <h2>Buka Hotline Instansi</h2>
    <p>Anda mengakses tautan resmi <?php echo APP_NAME; ?></p>
    <div class="hotline-grid">
      <div class="hotline-kartu"><div class="hk-ikon">&#127760;</div><h4>Website Resmi</h4><p>puskesmas-sehat.go.id</p><span class="terverifikasi">&#10003; Terverifikasi</span></div>
      <div class="hotline-kartu"><div class="hk-ikon">&#128222;</div><h4>Nomor Resmi</h4><p>(021) 123-4567</p><span class="terverifikasi">&#10003; Terverifikasi</span></div>
    </div>
    <p class="info-kotak">Pastikan akses melalui tautan atau nomor resmi di atas.</p>
    <div class="tombol-navigasi"><button type="button" class="tombol tombol-utama" onclick="gantilangkah(2)">Lanjutkan &rarr;</button></div>
  </div>

  <!-- LANGKAH 2 -->
  <div class="kotak-langkah" id="kotak2" style="<?php echo $langkahAktif!=2?'display:none':''; ?>">
    <h2>Pilih Layanan Poli</h2>
    <div class="poli-pilih-grid">
      <?php foreach($poliList as $p): ?>
      <label class="poli-pilih-item <?php echo $poliPilih==$p['id']?'terpilih':''; ?>">
        <input type="radio" name="poli_id" value="<?php echo $p['id']; ?>"
               data-nama="<?php echo htmlspecialchars($p['nama_poli']); ?>"
               <?php echo $poliPilih==$p['id']?'checked':''; ?> onchange="pilihPoli(this)">
        <strong><?php echo htmlspecialchars($p['nama_poli']); ?></strong>
        <small><?php echo htmlspecialchars($p['deskripsi']); ?></small>
      </label>
      <?php endforeach; ?>
    </div>
    <input type="hidden" name="nama_poli_display" id="namaPoliDisplay" value="<?php echo htmlspecialchars($_POST['nama_poli_display']??''); ?>">
    <div class="tombol-navigasi">
      <button type="button" class="tombol tombol-outline" onclick="gantilangkah(1)">&larr; Kembali</button>
      <button type="button" class="tombol tombol-utama"   onclick="validasiLanjut(2)">Lanjutkan &rarr;</button>
    </div>
  </div>

  <!-- LANGKAH 3 -->
  <div class="kotak-langkah" id="kotak3" style="<?php echo $langkahAktif!=3?'display:none':''; ?>">
    <h2>Data Identitas Pasien</h2>
    <div class="form-grid">
      <div class="form-grup"><label>Nama Lengkap <span class="wajib">*</span></label>
        <input type="text" name="nama_lengkap" placeholder="Nama sesuai KTP" value="<?php echo htmlspecialchars($_POST['nama_lengkap']??''); ?>" required></div>
      <div class="form-grup"><label>NIK 16 digit <span class="wajib">*</span></label>
        <input type="text" name="nik" maxlength="16" placeholder="NIK" value="<?php echo htmlspecialchars($_POST['nik']??''); ?>" required></div>
      <div class="form-grup"><label>Tanggal Lahir <span class="wajib">*</span></label>
        <input type="date" name="tanggal_lahir" value="<?php echo htmlspecialchars($_POST['tanggal_lahir']??''); ?>" required></div>
      <div class="form-grup"><label>Jenis Kelamin <span class="wajib">*</span></label>
        <select name="jenis_kelamin" required>
          <option value="">-- Pilih --</option>
          <option value="Laki-laki"  <?php echo (($_POST['jenis_kelamin']??'')==='Laki-laki'?'selected':''); ?>>Laki-laki</option>
          <option value="Perempuan"  <?php echo (($_POST['jenis_kelamin']??'')==='Perempuan'?'selected':''); ?>>Perempuan</option>
        </select></div>
      <div class="form-grup"><label>No. Telepon <span class="wajib">*</span></label>
        <input type="tel" name="no_telepon" placeholder="08xxxxxxxxxx" value="<?php echo htmlspecialchars($_POST['no_telepon']??''); ?>" required></div>
      <div class="form-grup"><label>Jaminan Kesehatan</label>
        <select name="jenis_jaminan">
          <option value="Umum">Umum</option>
          <option value="BPJS" <?php echo (($_POST['jenis_jaminan']??'')==='BPJS'?'selected':''); ?>>BPJS</option>
          <option value="Asuransi Lain" <?php echo (($_POST['jenis_jaminan']??'')==='Asuransi Lain'?'selected':''); ?>>Asuransi Lain</option>
        </select></div>
      <div class="form-grup"><label>No. Kartu Jaminan</label>
        <input type="text" name="no_jaminan" placeholder="No. BPJS / asuransi" value="<?php echo htmlspecialchars($_POST['no_jaminan']??''); ?>"></div>
      <div class="form-grup form-grup-penuh"><label>Alamat Lengkap <span class="wajib">*</span></label>
        <textarea name="alamat" rows="3" required><?php echo htmlspecialchars($_POST['alamat']??''); ?></textarea></div>
    </div>
    <div class="tombol-navigasi">
      <button type="button" class="tombol tombol-outline" onclick="gantilangkah(2)">&larr; Kembali</button>
      <button type="button" class="tombol tombol-utama"   onclick="validasiLanjut(3)">Lanjutkan &rarr;</button>
    </div>
  </div>

  <!-- LANGKAH 4 -->
  <div class="kotak-langkah" id="kotak4" style="<?php echo $langkahAktif!=4?'display:none':''; ?>">
    <h2>Pilih Tanggal Kunjungan</h2>
    <div class="tanggal-grid" id="tanggalGrid"></div>
    <input type="hidden" name="tanggal_kunjungan" id="tanggalKunjungan" value="<?php echo htmlspecialchars($_POST['tanggal_kunjungan']??''); ?>">
    <div class="tombol-navigasi">
      <button type="button" class="tombol tombol-outline" onclick="gantilangkah(3)">&larr; Kembali</button>
      <button type="button" class="tombol tombol-utama"   onclick="validasiLanjut(4)">Lanjutkan &rarr;</button>
    </div>
  </div>

  <!-- LANGKAH 5 -->
  <div class="kotak-langkah" id="kotak5" style="<?php echo $langkahAktif!=5?'display:none':''; ?>">
    <h2>Pemilihan Waktu (Sesi)</h2>
    <div class="sesi-daftar">
      <?php $sesiList=[['Sesi 1 (08:00-10:00)','Sesi 1 – Pagi Awal','08:00–10:00 WIB'],['Sesi 2 (10:00-12:00)','Sesi 2 – Pagi Akhir','10:00–12:00 WIB'],['Sesi 3 (13:00-15:00)','Sesi 3 – Siang','13:00–15:00 WIB']];
      foreach($sesiList as $s): ?>
      <label class="sesi-item <?php echo (($_POST['sesi']??'')===$s[0]?'terpilih':''); ?>">
        <input type="radio" name="sesi" value="<?php echo htmlspecialchars($s[0]); ?>"
               <?php echo (($_POST['sesi']??'')===$s[0]?'checked':''); ?> onchange="pilihanSesi(this)">
        <div class="sesi-info"><strong><?php echo $s[1]; ?></strong></div>
        <div class="sesi-waktu"><?php echo $s[2]; ?></div>
      </label>
      <?php endforeach; ?>
    </div>
    <div class="tombol-navigasi">
      <button type="button" class="tombol tombol-outline" onclick="gantilangkah(4)">&larr; Kembali</button>
      <button type="button" class="tombol tombol-utama"   onclick="validasiLanjut(5)">Lanjutkan &rarr;</button>
    </div>
  </div>

  <!-- LANGKAH 6 -->
  <div class="kotak-langkah" id="kotak6" style="<?php echo $langkahAktif!=6?'display:none':''; ?>">
    <h2>Konfirmasi Akhir</h2>
    <p>Periksa kembali data sebelum mengkonfirmasi</p>
    <table class="ringkasan-tabel" id="ringkasanTabel">
      <tr><td>Nama Lengkap</td>     <td id="rNama"><?php echo htmlspecialchars($_POST['nama_lengkap']??'-'); ?></td></tr>
      <tr><td>NIK</td>              <td id="rNik"><?php echo htmlspecialchars($_POST['nik']??'-'); ?></td></tr>
      <tr><td>Tanggal Lahir</td>    <td id="rLahir"><?php echo !empty($_POST['tanggal_lahir'])?date('d F Y',strtotime($_POST['tanggal_lahir'])):'-'; ?></td></tr>
      <tr><td>Jenis Kelamin</td>    <td id="rKelamin"><?php echo htmlspecialchars($_POST['jenis_kelamin']??'-'); ?></td></tr>
      <tr><td>No. Telepon</td>      <td id="rTelp"><?php echo htmlspecialchars($_POST['no_telepon']??'-'); ?></td></tr>
      <tr><td>Jaminan</td>          <td id="rJaminan"><?php echo htmlspecialchars($_POST['jenis_jaminan']??'-'); ?></td></tr>
      <tr><td>Poli Tujuan</td>      <td id="rPoli"><?php echo htmlspecialchars($_POST['nama_poli_display']??'-'); ?></td></tr>
      <tr><td>Tanggal Kunjungan</td><td id="rTanggal"><?php echo !empty($_POST['tanggal_kunjungan'])?date('l, d F Y',strtotime($_POST['tanggal_kunjungan'])):'-'; ?></td></tr>
      <tr><td>Sesi</td>             <td id="rSesi"><?php echo htmlspecialchars($_POST['sesi']??'-'); ?></td></tr>
    </table>
    <input type="hidden" name="konfirmasi" value="1">
    <div class="tombol-navigasi">
      <button type="button" class="tombol tombol-outline" onclick="gantilangkah(5)">&larr; Kembali</button>
      <button type="submit" class="tombol tombol-utama">&#10003; Ya, Konfirmasi &amp; Daftar</button>
    </div>
  </div>
</form>
<?php endif; ?>
</div></div></div>
<script src="assets/js/pendaftaran.js"></script>
<?php require_once 'includes/footer.php'; ?>