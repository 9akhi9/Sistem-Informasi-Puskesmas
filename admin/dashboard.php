<?php
require_once '../config/koneksi.php';
wajibLogin();
$db = koneksi();
$stat = [
  'total'        => $db->query("SELECT COUNT(*) AS c FROM pendaftaran")->fetch_assoc()['c'],
  'menunggu'     => $db->query("SELECT COUNT(*) AS c FROM pendaftaran WHERE status='Menunggu'")->fetch_assoc()['c'],
  'dikonfirmasi' => $db->query("SELECT COUNT(*) AS c FROM pendaftaran WHERE status='Dikonfirmasi'")->fetch_assoc()['c'],
  'selesai'      => $db->query("SELECT COUNT(*) AS c FROM pendaftaran WHERE status='Selesai'")->fetch_assoc()['c'],
  'dibatalkan'   => $db->query("SELECT COUNT(*) AS c FROM pendaftaran WHERE status='Dibatalkan'")->fetch_assoc()['c'],
];
$terbaru = $db->query("SELECT p.*, pl.nama_poli FROM pendaftaran p JOIN poli pl ON p.poli_id=pl.id ORDER BY p.created_at DESC LIMIT 10")->fetch_all(MYSQLI_ASSOC);
$perPoli = $db->query("SELECT pl.nama_poli, COUNT(p.id) AS total FROM poli pl LEFT JOIN pendaftaran p ON pl.id=p.poli_id GROUP BY pl.id,pl.nama_poli ORDER BY total DESC")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Dashboard – <?php echo APP_NAME; ?></title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="admin-body">
<?php include 'includes/sidebar.php'; ?>
<div class="admin-konten">
  <?php include 'includes/topbar.php'; ?>
  <div class="admin-utama">
    <h1 class="admin-judul">Dashboard</h1>
    <div class="stat-grid">
      <div class="stat-kartu biru"><p>Total</p><h2><?php echo $stat['total']; ?></h2></div>
      <div class="stat-kartu oranye"><p>Menunggu</p><h2><?php echo $stat['menunggu']; ?></h2></div>
      <div class="stat-kartu biru2"><p>Dikonfirmasi</p><h2><?php echo $stat['dikonfirmasi']; ?></h2></div>
      <div class="stat-kartu hijau"><p>Selesai</p><h2><?php echo $stat['selesai']; ?></h2></div>
      <div class="stat-kartu merah"><p>Dibatalkan</p><h2><?php echo $stat['dibatalkan']; ?></h2></div>
    </div>
    <div class="dash-grid">
      <div class="admin-kartu">
        <div class="kartu-header"><h3>Pendaftaran Terbaru</h3><a href="pendaftaran.php" class="tombol tombol-outline tombol-kecil">Lihat Semua</a></div>
        <div class="tabel-scroll">
        <table class="admin-tabel">
          <thead><tr><th>Kode</th><th>Nama</th><th>Poli</th><th>Tanggal</th><th>Status</th><th>Aksi</th></tr></thead>
          <tbody>
          <?php foreach($terbaru as $r): ?>
          <tr>
            <td><code><?php echo htmlspecialchars($r['kode_daftar']); ?></code></td>
            <td><?php echo htmlspecialchars($r['nama_lengkap']); ?></td>
            <td><?php echo htmlspecialchars($r['nama_poli']); ?></td>
            <td><?php echo date('d/m/Y',strtotime($r['tanggal_kunjungan'])); ?></td>
            <td><span class="status-badge status-<?php echo strtolower(str_replace(' ','-',$r['status'])); ?>"><?php echo $r['status']; ?></span></td>
            <td><a href="detail.php?id=<?php echo $r['id']; ?>" class="tombol tombol-kecil tombol-utama">Detail</a></td>
          </tr>
          <?php endforeach; ?>
          </tbody>
        </table></div>
      </div>
      <div class="admin-kartu">
        <div class="kartu-header"><h3>Per Poli</h3></div>
        <div class="poli-bar-list">
          <?php foreach($perPoli as $pp):
            $pct = $stat['total']>0 ? round(($pp['total']/$stat['total'])*100) : 0; ?>
          <div class="poli-bar-baris">
            <span><?php echo htmlspecialchars($pp['nama_poli']); ?></span>
            <div class="poli-bar-track"><div class="poli-bar-isi" style="width:<?php echo $pct; ?>%"></div></div>
            <strong><?php echo $pp['total']; ?></strong>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</div>
</body></html>