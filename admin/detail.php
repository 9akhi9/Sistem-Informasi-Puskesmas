<?php
require_once '../config/koneksi.php';
wajibLogin();
$db = koneksi();
$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: pendaftaran.php'); exit; }

// Simpan catatan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan_catatan'])) {
    $catatan = bersihkan($_POST['catatan'] ?? '');
    $status  = bersihkan($_POST['status']  ?? '');
    $stmt = $db->prepare("UPDATE pendaftaran SET catatan=?, status=? WHERE id=?");
    $stmt->bind_param('ssi', $catatan, $status, $id);
    $stmt->execute();
    header("Location: detail.php?id=$id&ok=1"); exit;
}

$r = $db->query("SELECT p.*, pl.nama_poli, pl.dokter FROM pendaftaran p JOIN poli pl ON p.poli_id=pl.id WHERE p.id=$id")->fetch_assoc();
if (!$r) { header('Location: pendaftaran.php'); exit; }

$warnaStatus = ['Menunggu'=>'status-menunggu','Dikonfirmasi'=>'status-dikonfirmasi','Selesai'=>'status-selesai','Dibatalkan'=>'status-dibatalkan'];
?>
<!DOCTYPE html><html lang="id"><head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Detail Pendaftaran – <?php echo APP_NAME; ?></title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/admin.css">
</head><body class="admin-body">
<?php include 'includes/sidebar.php'; ?>
<div class="admin-konten">
  <?php include 'includes/topbar.php'; ?>
  <div class="admin-utama">
    <div class="admin-page-header">
      <div>
        <a href="pendaftaran.php" class="tombol tombol-outline tombol-kecil">&larr; Kembali</a>
        <h1 class="admin-judul" style="margin-top:8px">Detail Pendaftaran</h1>
      </div>
      <span class="status-badge <?php echo $warnaStatus[$r['status']]??'status-menunggu'; ?>"><?php echo $r['status']; ?></span>
    </div>
    <?php if(isset($_GET['ok'])): ?><div class="pesan-sukses">Data berhasil disimpan.</div><?php endif; ?>

    <div class="dash-grid">
      <!-- Info Pasien -->
      <div class="admin-kartu">
        <div class="kartu-header"><h3>&#128100; Data Pasien</h3></div>
        <table class="detail-tabel">
          <tr><td>Kode Daftar</td>  <td><code><?php echo htmlspecialchars($r['kode_daftar']); ?></code></td></tr>
          <tr><td>Nama Lengkap</td> <td><strong><?php echo htmlspecialchars($r['nama_lengkap']); ?></strong></td></tr>
          <tr><td>NIK</td>          <td><?php echo htmlspecialchars($r['nik']); ?></td></tr>
          <tr><td>Tanggal Lahir</td><td><?php echo date('d F Y', strtotime($r['tanggal_lahir'])); ?></td></tr>
          <tr><td>Jenis Kelamin</td><td><?php echo htmlspecialchars($r['jenis_kelamin']); ?></td></tr>
          <tr><td>No. Telepon</td>  <td><?php echo htmlspecialchars($r['no_telepon']); ?></td></tr>
          <tr><td>Alamat</td>       <td><?php echo nl2br(htmlspecialchars($r['alamat'])); ?></td></tr>
          <tr><td>Jaminan</td>      <td><?php echo htmlspecialchars($r['jenis_jaminan']); ?><?php if($r['no_jaminan']): ?> — <?php echo htmlspecialchars($r['no_jaminan']); ?><?php endif; ?></td></tr>
        </table>
      </div>
      <!-- Info Kunjungan -->
      <div class="admin-kartu">
        <div class="kartu-header"><h3>&#128197; Data Kunjungan</h3></div>
        <table class="detail-tabel">
          <tr><td>Poli</td>           <td><strong><?php echo htmlspecialchars($r['nama_poli']); ?></strong></td></tr>
          <tr><td>Dokter</td>         <td><?php echo htmlspecialchars($r['dokter']); ?></td></tr>
          <tr><td>Tanggal Kunjungan</td><td><?php echo date('l, d F Y', strtotime($r['tanggal_kunjungan'])); ?></td></tr>
          <tr><td>Sesi</td>           <td><?php echo htmlspecialchars($r['sesi']); ?></td></tr>
          <tr><td>No. Antrian</td>    <td><strong style="font-size:1.5rem;color:#16a34a"><?php echo $r['nomor_antrian']; ?></strong></td></tr>
          <tr><td>Didaftarkan</td>    <td><?php echo date('d/m/Y H:i', strtotime($r['created_at'])); ?></td></tr>
          <tr><td>Diperbarui</td>     <td><?php echo date('d/m/Y H:i', strtotime($r['updated_at'])); ?></td></tr>
        </table>

        <!-- Form ubah status & catatan -->
        <form method="POST" style="margin-top:20px">
          <div class="form-grup">
            <label>Status</label>
            <select name="status">
              <?php foreach(['Menunggu','Dikonfirmasi','Selesai','Dibatalkan'] as $s): ?>
              <option value="<?php echo $s; ?>" <?php echo $r['status']===$s?'selected':''; ?>><?php echo $s; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-grup">
            <label>Catatan Admin</label>
            <textarea name="catatan" rows="4"><?php echo htmlspecialchars($r['catatan']??''); ?></textarea>
          </div>
          <input type="hidden" name="simpan_catatan" value="1">
          <button type="submit" class="tombol tombol-utama">Simpan Perubahan</button>
        </form>
      </div>
    </div>
  </div>
</div>
</body></html>