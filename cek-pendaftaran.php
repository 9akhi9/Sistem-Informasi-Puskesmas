<?php
require_once 'config/koneksi.php';
$judul  = 'Cek Pendaftaran';
$db     = koneksi();
$hasil  = [];
$dicari = false;
$query  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $query  = bersihkan($_POST['query'] ?? '');
    $dicari = true;
    if ($query !== '') {
        $cari = '%' . $query . '%';
        $stmt = $db->prepare("SELECT p.*, pl.nama_poli FROM pendaftaran p JOIN poli pl ON p.poli_id=pl.id WHERE p.kode_daftar LIKE ? OR p.nik LIKE ? ORDER BY p.created_at DESC");
        $stmt->bind_param('ss', $cari, $cari);
        $stmt->execute();
        $hasil = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
$warnaStatus = ['Menunggu'=>'status-menunggu','Dikonfirmasi'=>'status-dikonfirmasi','Selesai'=>'status-selesai','Dibatalkan'=>'status-dibatalkan'];
require_once 'includes/header.php';
?>
<div class="halaman-container"><div class="container"><div class="cek-wrapper">
  <div class="cek-header"><h1>Cek Status Pendaftaran</h1><p>Masukkan kode pendaftaran (PKM-...) atau NIK</p></div>
  <form method="POST" class="form-cari">
    <input type="text" name="query" placeholder="Kode pendaftaran atau NIK..." value="<?php echo htmlspecialchars($query); ?>" required>
    <button type="submit" class="tombol tombol-utama">&#128269; Cari</button>
  </form>
  <?php if ($dicari): ?>
    <?php if (empty($hasil)): ?>
    <div class="kosong-info"><div style="font-size:2.5rem">&#128269;</div><h3>Data Tidak Ditemukan</h3><p>Periksa kembali kode atau NIK Anda.</p></div>
    <?php else: ?>
    <div class="hasil-daftar">
      <?php foreach ($hasil as $r): ?>
      <div class="hasil-kartu">
        <div class="hasil-atas">
          <span class="hasil-kode"># <?php echo htmlspecialchars($r['kode_daftar']); ?></span>
          <span class="status-badge <?php echo $warnaStatus[$r['status']]??'status-menunggu'; ?>"><?php echo $r['status']; ?></span>
        </div>
        <table class="hasil-tabel">
          <tr><td>Nama</td>    <td><?php echo htmlspecialchars($r['nama_lengkap']); ?></td></tr>
          <tr><td>Poli</td>    <td><?php echo htmlspecialchars($r['nama_poli']); ?></td></tr>
          <tr><td>Tanggal</td> <td><?php echo date('l, d F Y', strtotime($r['tanggal_kunjungan'])); ?></td></tr>
          <tr><td>Sesi</td>    <td><?php echo htmlspecialchars($r['sesi']); ?></td></tr>
          <tr><td>Antrian</td> <td><strong><?php echo $r['nomor_antrian']; ?></strong></td></tr>
        </table>
        <?php if (!empty($r['catatan'])): ?>
        <div style="margin:10px 18px 14px;background:#fefce8;border:1px solid #fde047;border-radius:10px;padding:12px 16px;display:flex;gap:10px;align-items:flex-start;">
          <span style="font-size:1.1rem;flex-shrink:0;">&#128172;</span>
          <div>
            <strong style="font-size:0.78rem;color:#854d0e;display:block;margin-bottom:4px;">Catatan Admin</strong>
            <p style="margin:0;font-size:0.875rem;color:#713f12;line-height:1.6;"><?php echo nl2br(htmlspecialchars($r['catatan'])); ?></p>
          </div>
        </div>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  <?php endif; ?>
</div></div></div>
<?php require_once 'includes/footer.php'; ?>