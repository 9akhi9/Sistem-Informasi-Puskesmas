<?php
ob_start();
require_once '../config/koneksi.php';
wajibLogin();
$db = koneksi();

// TAMBAH
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['tambah'])) {
    $stmt = $db->prepare("INSERT INTO poli (nama_poli,deskripsi,dokter,jadwal,jam_operasional,kuota_per_sesi,status) VALUES (?,?,?,?,?,?,?)");
    $kuota = (int)$_POST['kuota_per_sesi'];
    $stmt->bind_param('sssssis',
        $_POST['nama_poli'], $_POST['deskripsi'], $_POST['dokter'],
        $_POST['jadwal'], $_POST['jam_operasional'], $kuota, $_POST['status']);
    $stmt->execute();
    header('Location: ' . BASE_URL . '/admin/poli.php?ok=tambah'); exit;
}
// EDIT
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['edit'])) {
    $kuota = (int)$_POST['kuota_per_sesi'];
    $stmt = $db->prepare("UPDATE poli SET nama_poli=?,deskripsi=?,dokter=?,jadwal=?,jam_operasional=?,kuota_per_sesi=?,status=? WHERE id=?");
    $stmt->bind_param('sssssisi',
        $_POST['nama_poli'], $_POST['deskripsi'], $_POST['dokter'],
        $_POST['jadwal'], $_POST['jam_operasional'], $kuota, $_POST['status'], $_POST['id']);
    $stmt->execute();
    header('Location: ' . BASE_URL . '/admin/poli.php?ok=edit'); exit;
}
// HAPUS
if (isset($_GET['hapus']) && (int)$_GET['hapus'] > 0) {
    $hapusId = (int)$_GET['hapus'];
    $stmt = $db->prepare("DELETE FROM poli WHERE id=?");
    $stmt->bind_param('i', $hapusId);
    $stmt->execute();
    header('Location: ' . BASE_URL . '/admin/poli.php?ok=hapus'); exit;
}

$poliList = $db->query("SELECT p.*, COUNT(d.id) AS jml_daftar FROM poli p LEFT JOIN pendaftaran d ON p.id=d.poli_id GROUP BY p.id ORDER BY p.id")->fetch_all(MYSQLI_ASSOC);
$editData = null;
if (isset($_GET['edit'])) {
    $editData = $db->query("SELECT * FROM poli WHERE id=".(int)$_GET['edit'])->fetch_assoc();
}
?>
<!DOCTYPE html><html lang="id"><head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Manajemen Poli – <?php echo APP_NAME; ?></title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/admin.css">
</head><body class="admin-body">
<?php include 'includes/sidebar.php'; ?>
<div class="admin-konten">
  <?php include 'includes/topbar.php'; ?>
  <div class="admin-utama">
    <h1 class="admin-judul">Manajemen Poli</h1>
    <?php if(isset($_GET['ok'])): ?><div class="pesan-sukses">Operasi berhasil.</div><?php endif; ?>

    <!-- Form Tambah / Edit -->
    <div class="admin-kartu" style="margin-bottom:24px">
      <div class="kartu-header"><h3><?php echo $editData ? '&#9999; Edit Poli' : '&#10133; Tambah Poli Baru'; ?></h3></div>
      <form method="POST">
        <?php if($editData): ?><input type="hidden" name="id" value="<?php echo $editData['id']; ?>"><?php endif; ?>
        <div class="form-grid">
          <div class="form-grup">
            <label>Nama Poli <span class="wajib">*</span></label>
            <input type="text" name="nama_poli" value="<?php echo htmlspecialchars($editData['nama_poli']??''); ?>" required>
          </div>
          <div class="form-grup">
            <label>Dokter</label>
            <input type="text" name="dokter" value="<?php echo htmlspecialchars($editData['dokter']??''); ?>">
          </div>
          <div class="form-grup">
            <label>Jadwal</label>
            <input type="text" name="jadwal" placeholder="Senin - Jumat" value="<?php echo htmlspecialchars($editData['jadwal']??''); ?>">
          </div>
          <div class="form-grup">
            <label>Jam Operasional</label>
            <input type="text" name="jam_operasional" placeholder="08:00 - 15:00" value="<?php echo htmlspecialchars($editData['jam_operasional']??''); ?>">
          </div>
          <div class="form-grup">
            <label>Kuota per Sesi</label>
            <input type="number" name="kuota_per_sesi" min="1" max="100" value="<?php echo htmlspecialchars($editData['kuota_per_sesi']??20); ?>">
          </div>
          <div class="form-grup">
            <label>Status</label>
            <select name="status">
              <option value="aktif"    <?php echo (($editData['status']??'aktif')==='aktif'   ?'selected':''); ?>>Aktif</option>
              <option value="nonaktif" <?php echo (($editData['status']??'')==='nonaktif'?'selected':''); ?>>Nonaktif</option>
            </select>
          </div>
          <div class="form-grup form-grup-penuh">
            <label>Deskripsi</label>
            <textarea name="deskripsi" rows="2"><?php echo htmlspecialchars($editData['deskripsi']??''); ?></textarea>
          </div>
        </div>
        <div style="display:flex;gap:10px;margin-top:8px">
          <button type="submit" name="<?php echo $editData?'edit':'tambah'; ?>" class="tombol tombol-utama">
            <?php echo $editData ? 'Simpan Perubahan' : 'Tambah Poli'; ?>
          </button>
          <?php if($editData): ?>
          <a href="poli.php" class="tombol tombol-outline">Batal</a>
          <?php endif; ?>
        </div>
      </form>
    </div>

    <!-- Tabel Poli -->
    <div class="admin-kartu"><div class="tabel-scroll">
    <table class="admin-tabel">
      <thead><tr><th>#</th><th>Nama Poli</th><th>Dokter</th><th>Jadwal</th><th>Jam</th><th>Kuota</th><th>Daftar</th><th>Status</th><th>Aksi</th></tr></thead>
      <tbody>
      <?php foreach($poliList as $i=>$p): ?>
      <tr>
        <td><?php echo $i+1; ?></td>
        <td><strong><?php echo htmlspecialchars($p['nama_poli']); ?></strong><br><small class="muted"><?php echo htmlspecialchars(substr($p['deskripsi'],0,50)); ?>...</small></td>
        <td><?php echo htmlspecialchars($p['dokter']); ?></td>
        <td><?php echo htmlspecialchars($p['jadwal']); ?></td>
        <td><?php echo htmlspecialchars($p['jam_operasional']); ?></td>
        <td style="text-align:center"><?php echo $p['kuota_per_sesi']; ?></td>
        <td style="text-align:center"><strong class="hijau"><?php echo $p['jml_daftar']; ?></strong></td>
        <td><span class="status-badge <?php echo $p['status']==='aktif'?'status-selesai':'status-dibatalkan'; ?>"><?php echo $p['status']; ?></span></td>
        <td>
          <a href="?edit=<?php echo $p['id']; ?>" class="tombol tombol-kecil tombol-utama">Edit</a>
          <a href="?hapus=<?php echo $p['id']; ?>" class="tombol tombol-kecil tombol-hapus" onclick="return confirm('Hapus poli ini?')">Hapus</a>
        </td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table></div></div>
  </div>
</div>
</body></html>