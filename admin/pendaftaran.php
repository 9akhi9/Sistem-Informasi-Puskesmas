<?php
ob_start();
require_once '../config/koneksi.php';
wajibLogin();
$db = koneksi();

if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['ubah_status'])) {
    $id=(int)$_POST['id']; $status=bersihkan($_POST['status']);
    $stmt=$db->prepare("UPDATE pendaftaran SET status=? WHERE id=?");
    $stmt->bind_param('si',$status,$id); $stmt->execute();
    ob_end_clean();
    header('Location: ' . BASE_URL . '/admin/pendaftaran.php?ok=1');
    exit;
}
if (isset($_GET['hapus']) && (int)$_GET['hapus'] > 0) {
    $hapusId = (int)$_GET['hapus'];
    $stmt = $db->prepare("DELETE FROM pendaftaran WHERE id=?");
    $stmt->bind_param('i', $hapusId);
    $stmt->execute();
    ob_end_clean();
    header('Location: ' . BASE_URL . '/admin/pendaftaran.php?terhapus=1');
    exit;
}

$where='1=1'; $fS=$_GET['status']??''; $fP=$_GET['poli']??''; $fT=$_GET['tanggal']??''; $fQ=$_GET['q']??'';
if($fS) $where.=" AND p.status='".$db->real_escape_string($fS)."'";
if($fP) $where.=" AND p.poli_id=".(int)$fP;
if($fT) $where.=" AND p.tanggal_kunjungan='".$db->real_escape_string($fT)."'";
if($fQ) $where.=" AND (p.nama_lengkap LIKE '%".$db->real_escape_string($fQ)."%' OR p.kode_daftar LIKE '%".$db->real_escape_string($fQ)."%' OR p.nik LIKE '%".$db->real_escape_string($fQ)."%')";

$hal=max(1,(int)($_GET['hal']??1)); $perHal=15; $offset=($hal-1)*$perHal;
$total=$db->query("SELECT COUNT(*) AS c FROM pendaftaran p WHERE $where")->fetch_assoc()['c'];
$totalHal=ceil($total/$perHal);
$data=$db->query("SELECT p.*,pl.nama_poli FROM pendaftaran p JOIN poli pl ON p.poli_id=pl.id WHERE $where ORDER BY p.created_at DESC LIMIT $perHal OFFSET $offset")->fetch_all(MYSQLI_ASSOC);
$poliList=$db->query("SELECT * FROM poli WHERE status='aktif'")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html><html lang="id"><head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Pendaftaran – <?php echo APP_NAME; ?></title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/admin.css">
</head><body class="admin-body">
<?php include 'includes/sidebar.php'; ?>
<div class="admin-konten">
  <?php include 'includes/topbar.php'; ?>
  <div class="admin-utama">
    <div class="admin-page-header">
      <h1 class="admin-judul">Manajemen Pendaftaran</h1>
      <span class="badge-jumlah"><?php echo $total; ?> data</span>
    </div>
    <?php if(isset($_GET['ok'])): ?><div class="pesan-sukses">Status diperbarui.</div><?php endif; ?>
    <?php if(isset($_GET['terhapus'])): ?><div class="pesan-sukses">Data berhasil dihapus.</div><?php endif; ?>
    <form method="GET" class="form-filter">
      <input type="text" name="q" placeholder="Cari nama/kode/NIK" value="<?php echo htmlspecialchars($fQ); ?>">
      <select name="status"><option value="">Semua Status</option>
        <?php foreach(['Menunggu','Dikonfirmasi','Selesai','Dibatalkan'] as $s): ?>
        <option value="<?php echo $s; ?>" <?php echo $fS===$s?'selected':''; ?>><?php echo $s; ?></option>
        <?php endforeach; ?></select>
      <select name="poli"><option value="">Semua Poli</option>
        <?php foreach($poliList as $p): ?>
        <option value="<?php echo $p['id']; ?>" <?php echo $fP==$p['id']?'selected':''; ?>><?php echo htmlspecialchars($p['nama_poli']); ?></option>
        <?php endforeach; ?></select>
      <input type="date" name="tanggal" value="<?php echo htmlspecialchars($fT); ?>">
      <button type="submit" class="tombol tombol-utama tombol-kecil">Filter</button>
      <a href="pendaftaran.php" class="tombol tombol-outline tombol-kecil">Reset</a>
    </form>
    <div class="admin-kartu"><div class="tabel-scroll">
    <table class="admin-tabel">
      <thead><tr><th>#</th><th>Kode</th><th>Nama/NIK</th><th>Poli</th><th>Tanggal</th><th>Sesi</th><th>No</th><th>Status</th><th>Aksi</th></tr></thead>
      <tbody>
      <?php foreach($data as $i=>$r): ?>
      <tr>
        <td><?php echo $offset+$i+1; ?></td>
        <td><code><?php echo htmlspecialchars($r['kode_daftar']); ?></code></td>
        <td><strong><?php echo htmlspecialchars($r['nama_lengkap']); ?></strong><br><small><?php echo htmlspecialchars($r['nik']); ?></small></td>
        <td><?php echo htmlspecialchars($r['nama_poli']); ?></td>
        <td><?php echo date('d/m/Y',strtotime($r['tanggal_kunjungan'])); ?></td>
        <td><?php echo htmlspecialchars($r['sesi']); ?></td>
        <td style="text-align:center"><strong><?php echo $r['nomor_antrian']; ?></strong></td>
        <td>
          <form method="POST" style="display:inline">
            <input type="hidden" name="id" value="<?php echo $r['id']; ?>">
            <select name="status" class="status-pilih status-<?php echo strtolower(str_replace(' ','-',$r['status'])); ?>" onchange="this.form.submit()">
              <?php foreach(['Menunggu','Dikonfirmasi','Selesai','Dibatalkan'] as $s): ?>
              <option value="<?php echo $s; ?>" <?php echo $r['status']===$s?'selected':''; ?>><?php echo $s; ?></option>
              <?php endforeach; ?>
            </select>
            <input type="hidden" name="ubah_status" value="1">
          </form>
        </td>
        <td>
          <a href="detail.php?id=<?php echo $r['id']; ?>" class="tombol tombol-kecil tombol-utama">Detail</a>
          <a href="?hapus=<?php echo $r['id']; ?>" class="tombol tombol-kecil tombol-hapus" onclick="return confirm('Hapus?')">Hapus</a>
        </td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table></div></div>
    <?php if($totalHal>1): ?>
    <div class="paginasi">
      <?php for($h=1;$h<=$totalHal;$h++): ?>
      <a href="?hal=<?php echo $h; ?>&status=<?php echo urlencode($fS); ?>&poli=<?php echo $fP; ?>&tanggal=<?php echo urlencode($fT); ?>&q=<?php echo urlencode($fQ); ?>"
         class="paginasi-tombol <?php echo $h==$hal?'aktif':''; ?>"><?php echo $h; ?></a>
      <?php endfor; ?>
    </div>
    <?php endif; ?>
  </div>
</div>
</body></html>